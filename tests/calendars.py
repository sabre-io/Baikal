import mechanicalsoup
from requests.auth import HTTPDigestAuth
from lib.backends import Backend
from lib.helpers import (
    BASE_URL, follow_link_containing, follow_meta_redirect, find_and_follow_row_link,
)

def create_test_user(browser: mechanicalsoup.StatefulBrowser):
    follow_link_containing(browser, "users and resources")
    follow_link_containing(browser, "add user")
    browser.select_form("form")
    browser["data[username]"] = "caluser"
    browser["data[displayname]"] = "Calendar User"
    browser["data[email]"] = "caluser@example.com"
    browser["data[password]"] = "password123"
    browser["data[passwordconfirm]"] = "password123"
    browser.submit_selected()

def navigate_to_user_calendars(browser: mechanicalsoup.StatefulBrowser):
    follow_link_containing(browser, "users and resources")
    find_and_follow_row_link(browser, "caluser", "calendars")

def create_test_calendar(browser: mechanicalsoup.StatefulBrowser):
    navigate_to_user_calendars(browser)
    follow_link_containing(browser, "add calendar")
    browser.select_form("form")
    browser["data[uri]"] = "test-calendar"
    browser["data[displayname]"] = "Test Calendar"
    browser["data[description]"] = "A test calendar"
    browser.submit_selected()

def test_calendar_create(browser: mechanicalsoup.StatefulBrowser, backend: Backend):
    backend.install(browser)
    create_test_user(browser)

    navigate_to_user_calendars(browser)
    page = browser.get_current_page()
    assert "calendars" in page.text.lower()
    assert "caluser" in page.text.lower()
    assert "default calendar" in page.text.lower()

    follow_link_containing(browser, "add calendar")
    browser.select_form("form")
    browser["data[uri]"] = "test-calendar"
    browser["data[displayname]"] = "Test Calendar"
    browser["data[description]"] = "A test calendar"
    browser.submit_selected()

    page = browser.get_current_page()
    assert "test calendar" in page.text.lower()

def test_calendar_create_invalid_uri(browser: mechanicalsoup.StatefulBrowser, backend: Backend):
    backend.install(browser)
    create_test_user(browser)

    navigate_to_user_calendars(browser)
    follow_link_containing(browser, "add calendar")
    browser.select_form("form")
    browser["data[uri]"] = "Invalid URI!"
    browser["data[displayname]"] = "Test Calendar"
    browser.submit_selected()

    page = browser.get_current_page()
    assert "validation error" in page.text.lower()
    assert "is not valid" in page.text.lower()

def test_calendar_create_missing_displayname(browser: mechanicalsoup.StatefulBrowser, backend: Backend):
    backend.install(browser)
    create_test_user(browser)

    navigate_to_user_calendars(browser)
    follow_link_containing(browser, "add calendar")
    browser.select_form("form")
    browser["data[uri]"] = "test-calendar"
    browser["data[displayname]"] = ""
    browser.submit_selected()

    page = browser.get_current_page()
    assert "validation error" in page.text.lower()
    assert "display name" in page.text.lower()
    assert "is required" in page.text.lower()

def test_calendar_edit(browser: mechanicalsoup.StatefulBrowser, backend: Backend):
    backend.install(browser)
    create_test_user(browser)
    create_test_calendar(browser)

    # The edit form is shown right after creation; update the display name
    browser.select_form("form")
    browser["data[displayname]"] = "Test Calendar Updated"
    browser["data[description]"] = "Updated description"
    browser.submit_selected()

    page = browser.get_current_page()
    assert "test calendar updated" in page.text.lower()

def test_calendar_delete(browser: mechanicalsoup.StatefulBrowser, backend: Backend):
    backend.install(browser)
    create_test_user(browser)
    create_test_calendar(browser)

    navigate_to_user_calendars(browser)
    find_and_follow_row_link(browser, "test calendar", "delete")
    follow_link_containing(browser, "delete test calendar")
    follow_meta_redirect(browser)

    page = browser.get_current_page()
    assert "test calendar" not in page.text.lower()

def dav_url(uri: str) -> str:
    return f"{BASE_URL}dav.php/calendars/caluser/{uri}/"

DAV_AUTH = HTTPDigestAuth("caluser", "password123")

PROPFIND_BODY = """<?xml version="1.0" encoding="utf-8"?>
<D:propfind xmlns:D="DAV:" xmlns:C="urn:ietf:params:xml:ns:caldav">
  <D:prop>
    <C:calendar-timezone/>
  </D:prop>
</D:propfind>"""

EXPAND_REPORT_BODY = """<?xml version="1.0" encoding="utf-8"?>
<C:calendar-query xmlns:D="DAV:" xmlns:C="urn:ietf:params:xml:ns:caldav">
  <D:prop>
    <D:getetag/>
    <C:calendar-data>
      <C:expand start="20240101T000000Z" end="20240131T000000Z"/>
    </C:calendar-data>
  </D:prop>
  <C:filter>
    <C:comp-filter name="VCALENDAR">
      <C:comp-filter name="VEVENT">
        <C:time-range start="20240101T000000Z" end="20240131T000000Z"/>
      </C:comp-filter>
    </C:comp-filter>
  </C:filter>
</C:calendar-query>"""

TEST_EVENT = """BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//Baikal Test//EN
BEGIN:VEVENT
UID:expand-test@example.com
DTSTAMP:20240101T000000Z
DTSTART:20240105T100000Z
DTEND:20240105T110000Z
SUMMARY:Expand test
END:VEVENT
END:VCALENDAR
"""

def test_calendar_timezone_is_vtimezone(browser: mechanicalsoup.StatefulBrowser, backend: Backend):
    # A calendar-timezone holding the bare identifier makes every expanded
    # calendar-query fail, see https://github.com/sabre-io/Baikal/issues/1241
    backend.install(browser)
    create_test_user(browser)
    create_test_calendar(browser)

    response = browser.session.request(
        "PROPFIND", dav_url("test-calendar"),
        data=PROPFIND_BODY,
        headers={"Content-Type": "application/xml; charset=utf-8", "Depth": "0"},
        auth=DAV_AUTH,
    )
    assert response.status_code == 207, response.status_code
    assert "BEGIN:VCALENDAR" in response.text, response.text
    assert "BEGIN:VTIMEZONE" in response.text, response.text

def test_calendar_report_with_expand(browser: mechanicalsoup.StatefulBrowser, backend: Backend):
    backend.install(browser)
    create_test_user(browser)
    create_test_calendar(browser)

    put = browser.session.put(
        dav_url("test-calendar") + "expand-test.ics",
        data=TEST_EVENT,
        headers={"Content-Type": "text/calendar; charset=utf-8"},
        auth=DAV_AUTH,
    )
    assert put.status_code in (201, 204), put.status_code

    response = browser.session.request(
        "REPORT", dav_url("test-calendar"),
        data=EXPAND_REPORT_BODY,
        headers={"Content-Type": "application/xml; charset=utf-8", "Depth": "1"},
        auth=DAV_AUTH,
    )
    assert response.status_code == 207, (response.status_code, response.text)
    assert "Expand test" in response.text, response.text
