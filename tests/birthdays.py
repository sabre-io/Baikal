import re
from datetime import date
from urllib.parse import urljoin

import mechanicalsoup
import requests
from requests.auth import HTTPDigestAuth

from lib import backends
from lib.backends import Backend
from lib.helpers import (
    BASE_URL, follow_link_containing, follow_meta_redirect, find_and_follow_row_link,
)

BACKENDS = backends.ALL

USERNAME = "bdayuser"
PASSWORD = "password123"
AUTH = HTTPDigestAuth(USERNAME, PASSWORD)
ADDRESSBOOK_URL = BASE_URL + f"card.php/addressbooks/{USERNAME}/default/"
CALENDAR_URL = BASE_URL + f"cal.php/calendars/{USERNAME}/contact_birthdays/"

def create_test_user(browser: mechanicalsoup.StatefulBrowser):
    follow_link_containing(browser, "users and resources")
    follow_link_containing(browser, "add user")
    browser.select_form("form")
    browser["data[username]"] = USERNAME
    browser["data[displayname]"] = "Birthday User"
    browser["data[email]"] = "bdayuser@example.com"
    browser["data[password]"] = PASSWORD
    browser["data[passwordconfirm]"] = PASSWORD
    browser.submit_selected()

def navigate_to_user_calendars(browser: mechanicalsoup.StatefulBrowser):
    follow_link_containing(browser, "users and resources")
    find_and_follow_row_link(browser, USERNAME, "calendars")

def submit_button_form(browser: mechanicalsoup.StatefulBrowser, row_text: str, button_text: str):
    """Submits the form of the first button containing button_text in an element containing row_text."""
    page = browser.get_current_page()
    for container in page.find_all(["tr", "div"]):
        if row_text.lower() not in container.get_text(strip=True).lower():
            continue
        for button in container.find_all("button"):
            if button_text.lower() in button.get_text(strip=True).lower():
                browser.select_form(f"form#{button['form']}")
                browser.submit_selected()
                return
    raise RuntimeError(f"No element containing '{row_text}' with button '{button_text}' found on page")

def create_birthday_calendar(browser: mechanicalsoup.StatefulBrowser):
    navigate_to_user_calendars(browser)
    submit_button_form(browser, "add birthday calendar", "add birthday calendar")
    follow_meta_redirect(browser)

def vcard(name: str, bday: str | None) -> str:
    lines = ["BEGIN:VCARD", "VERSION:3.0", f"UID:{name}", f"FN:{name}", f"N:{name};;;;"]
    if bday is not None:
        lines.append(f"BDAY:{bday}")
    lines.append("END:VCARD")
    return "\r\n".join(lines) + "\r\n"

def put_card(url: str, data: str) -> int:
    response = requests.put(url, data=data.encode(), auth=AUTH, headers={"Content-Type": "text/vcard"})
    assert response.status_code in (201, 204), f"PUT {url} returned {response.status_code}"
    return response.status_code

def get_birthday_events() -> list[str]:
    """Returns the iCalendar data of all events in the birthday calendar."""
    response = requests.request("PROPFIND", CALENDAR_URL, auth=AUTH, headers={"Depth": "1"})
    assert response.status_code == 207, f"PROPFIND returned {response.status_code}"
    root = BASE_URL.split("/html/")[0]
    hrefs = sorted(set(re.findall(r"<d:href>([^<]+\.ics)</d:href>", response.text)))
    return [requests.get(root + href, auth=AUTH).text for href in hrefs]

def summaries() -> list[str]:
    return sorted(
        summary.strip()
        for data in get_birthday_events()
        for summary in re.findall(r"^SUMMARY:(.*)$", data, re.MULTILINE)
    )

def test_birthday_calendar_follows_contacts(browser: mechanicalsoup.StatefulBrowser, backend: Backend):
    backend.install(browser)
    create_test_user(browser)

    # Contacts existing before the calendar is created are included
    put_card(ADDRESSBOOK_URL + "anna.vcf", vcard("Anna Example", "1990-03-15"))
    create_birthday_calendar(browser)

    page = browser.get_current_page()
    assert "birthdays" in page.text.lower()
    assert "add birthday calendar" not in page.text.lower()

    age = date.today().year - 1990
    assert f"🎂 Anna Example ({age})" in summaries()
    assert "🎂 Anna Example" in summaries()

    # Created, updated and deleted contacts are reflected
    put_card(ADDRESSBOOK_URL + "bob.vcf", vcard("Bob Example", "--0701"))
    assert summaries().count("🎂 Bob Example") == 1

    put_card(ADDRESSBOOK_URL + "bob.vcf", vcard("Bobby Example", "--0701"))
    assert "🎂 Bobby Example" in summaries()
    assert "🎂 Bob Example" not in summaries()

    assert requests.delete(ADDRESSBOOK_URL + "bob.vcf", auth=AUTH).status_code == 204
    assert not any("Bobby" in summary for summary in summaries())

    # Removing the birthday removes the event
    put_card(ADDRESSBOOK_URL + "anna.vcf", vcard("Anna Example", None))
    assert get_birthday_events() == []

def test_birthday_calendar_read_only(browser: mechanicalsoup.StatefulBrowser, backend: Backend):
    backend.install(browser)
    create_test_user(browser)
    put_card(ADDRESSBOOK_URL + "anna.vcf", vcard("Anna Example", "1990-03-15"))
    create_birthday_calendar(browser)

    event = "BEGIN:VCALENDAR\r\nVERSION:2.0\r\nPRODID:test\r\nBEGIN:VEVENT\r\nUID:test\r\nDTSTAMP:20260101T000000Z\r\nDTSTART:20260101T100000Z\r\nSUMMARY:Test\r\nEND:VEVENT\r\nEND:VCALENDAR\r\n"
    headers = {"Content-Type": "text/calendar"}
    assert requests.put(CALENDAR_URL + "test.ics", data=event, auth=AUTH, headers=headers).status_code == 403

    response = requests.request("PROPFIND", CALENDAR_URL, auth=AUTH, headers={"Depth": "1"})
    href = re.search(r"<d:href>([^<]+\.ics)</d:href>", response.text).group(1)
    event_url = BASE_URL.split("/html/")[0] + href
    assert requests.put(event_url, data=event, auth=AUTH, headers=headers).status_code == 403
    assert requests.delete(event_url, auth=AUTH).status_code == 403

    # Other calendars are still writable
    default_url = BASE_URL + f"cal.php/calendars/{USERNAME}/default/test.ics"
    assert requests.put(default_url, data=event, auth=AUTH, headers=headers).status_code == 201

def test_birthday_calendar_rebuild_and_delete(browser: mechanicalsoup.StatefulBrowser, backend: Backend):
    backend.install(browser)
    create_test_user(browser)
    put_card(ADDRESSBOOK_URL + "anna.vcf", vcard("Anna Example", "1990-03-15"))
    create_birthday_calendar(browser)

    submit_button_form(browser, "birthdays", "rebuild")
    follow_meta_redirect(browser)
    assert "🎂 Anna Example" in summaries()

    find_and_follow_row_link(browser, "birthdays", "delete")
    follow_link_containing(browser, "delete birthdays")
    follow_meta_redirect(browser)

    page = browser.get_current_page()
    assert "add birthday calendar" in page.text.lower()
    assert requests.request("PROPFIND", CALENDAR_URL, auth=AUTH, headers={"Depth": "0"}).status_code == 404

    # The calendar can be created again
    submit_button_form(browser, "add birthday calendar", "add birthday calendar")
    follow_meta_redirect(browser)
    assert "🎂 Anna Example" in summaries()

def test_birthday_calendar_actions_require_post(browser: mechanicalsoup.StatefulBrowser, backend: Backend):
    backend.install(browser)
    create_test_user(browser)
    put_card(ADDRESSBOOK_URL + "anna.vcf", vcard("Anna Example", "1990-03-15"))
    navigate_to_user_calendars(browser)

    # A plain link, e.g. embedded in another site, does not create the calendar
    form = browser.get_current_page().find("form", id="birthdays-create")
    browser.open(urljoin(browser.url, form["action"]))
    assert requests.request("PROPFIND", CALENDAR_URL, auth=AUTH, headers={"Depth": "0"}).status_code == 404

    # Neither does a POST without the CSRF token
    browser.session.post(urljoin(browser.url, form["action"]))
    assert requests.request("PROPFIND", CALENDAR_URL, auth=AUTH, headers={"Depth": "0"}).status_code == 404

def test_birthday_calendar_address_book_delete(browser: mechanicalsoup.StatefulBrowser, backend: Backend):
    backend.install(browser)
    create_test_user(browser)

    follow_link_containing(browser, "users and resources")
    find_and_follow_row_link(browser, USERNAME, "address books")
    follow_link_containing(browser, "add address book")
    browser.select_form("form")
    browser["data[uri]"] = "work"
    browser["data[displayname]"] = "Work"
    browser.submit_selected()

    put_card(ADDRESSBOOK_URL + "anna.vcf", vcard("Anna Example", "1990-03-15"))
    put_card(BASE_URL + f"card.php/addressbooks/{USERNAME}/work/carl.vcf", vcard("Carl Example", "1980-01-01"))
    create_birthday_calendar(browser)
    assert "🎂 Carl Example" in summaries()

    follow_link_containing(browser, "users and resources")
    find_and_follow_row_link(browser, USERNAME, "address books")
    find_and_follow_row_link(browser, "work", "delete")
    follow_link_containing(browser, "delete work")
    follow_meta_redirect(browser)

    assert "🎂 Carl Example" not in summaries()
    assert "🎂 Anna Example" in summaries()

def test_birthday_calendar_reserved_uri(browser: mechanicalsoup.StatefulBrowser, backend: Backend):
    backend.install(browser)
    create_test_user(browser)

    navigate_to_user_calendars(browser)
    follow_link_containing(browser, "add calendar")
    browser.select_form("form")
    browser["data[uri]"] = "contact_birthdays"
    browser["data[displayname]"] = "My Birthdays"
    browser.submit_selected()

    page = browser.get_current_page()
    assert "reserved for the birthday calendar" in page.text.lower()

def test_no_birthday_calendar_by_default(browser: mechanicalsoup.StatefulBrowser, backend: Backend):
    backend.install(browser)
    create_test_user(browser)

    assert put_card(ADDRESSBOOK_URL + "anna.vcf", vcard("Anna Example", "1990-03-15")) == 201
    assert requests.request("PROPFIND", CALENDAR_URL, auth=AUTH, headers={"Depth": "0"}).status_code == 404

    navigate_to_user_calendars(browser)
    page = browser.get_current_page()
    assert "add birthday calendar" in page.text.lower()

def test_birthday_calendar_not_default_calendar(browser: mechanicalsoup.StatefulBrowser, backend: Backend):
    backend.install(browser)
    create_test_user(browser)
    create_birthday_calendar(browser)

    def default_calendar() -> str:
        body = '<?xml version="1.0"?><d:propfind xmlns:d="DAV:" xmlns:c="urn:ietf:params:xml:ns:caldav"><d:prop><c:schedule-default-calendar-URL/></d:prop></d:propfind>'
        response = requests.request("PROPFIND", BASE_URL + f"cal.php/principals/{USERNAME}/", data=body, auth=AUTH, headers={"Depth": "0"})
        assert response.status_code == 207, f"PROPFIND returned {response.status_code}"
        return response.text

    # Invitations are delivered to the default calendar, bypassing the ACL
    assert f"calendars/{USERNAME}/default" in default_calendar()

    assert requests.delete(BASE_URL + f"cal.php/calendars/{USERNAME}/default/", auth=AUTH).status_code == 204
    assert "contact_birthdays" not in default_calendar()

def test_birthday_calendar_marker_protected(browser: mechanicalsoup.StatefulBrowser, backend: Backend):
    backend.install(browser)
    create_test_user(browser)
    put_card(ADDRESSBOOK_URL + "anna.vcf", vcard("Anna Example", "1990-03-15"))
    create_birthday_calendar(browser)

    def proppatch(url: str, action: str) -> str:
        value = "<b:birthday-reference-year>2000</b:birthday-reference-year>" if action == "set" else "<b:birthday-reference-year/>"
        body = f'<?xml version="1.0"?><d:propertyupdate xmlns:d="DAV:" xmlns:b="http://sabre.io/baikal"><d:{action}><d:prop>{value}</d:prop></d:{action}></d:propertyupdate>'
        response = requests.request("PROPPATCH", url, data=body, auth=AUTH)
        assert response.status_code == 207, f"PROPPATCH returned {response.status_code}"
        return response.text

    # The marker can neither be removed from the birthday calendar ...
    assert "403" in proppatch(CALENDAR_URL, "remove")
    event = "BEGIN:VCALENDAR\r\nVERSION:2.0\r\nPRODID:test\r\nBEGIN:VEVENT\r\nUID:test\r\nDTSTAMP:20260101T000000Z\r\nDTSTART:20260101T100000Z\r\nSUMMARY:Test\r\nEND:VEVENT\r\nEND:VCALENDAR\r\n"
    headers = {"Content-Type": "text/calendar"}
    assert requests.put(CALENDAR_URL + "test.ics", data=event, auth=AUTH, headers=headers).status_code == 403
    assert "🎂 Anna Example" in summaries()

    # ... nor be set on other calendars
    assert "403" in proppatch(BASE_URL + f"cal.php/calendars/{USERNAME}/default/", "set")
