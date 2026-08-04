import mechanicalsoup
import os
from test_helpers import (
    ROOT_DIR, assert_dashboard, assert_installed, setup_admin_password,
    follow_link_containing, find_and_follow_row_link,
)

XSS_PAYLOAD = '<script>alert("xss")</script>'

def setup():
    db_path = os.path.join(ROOT_DIR, "Specific/db/db.sqlite")
    if os.path.exists(db_path):
        os.remove(db_path)

def install_sqlite(browser: mechanicalsoup.StatefulBrowser):
    setup_admin_password(browser)

    page = browser.get_current_page()
    assert "baïkal database setup" in page.text.lower()
    browser.select_form("form")
    browser.submit_selected()

    assert_installed(browser)
    assert_dashboard(browser)

def create_test_user(browser: mechanicalsoup.StatefulBrowser, username="abuser", displayname="Test User"):
    follow_link_containing(browser, "users and resources")
    follow_link_containing(browser, "add user")
    browser.select_form("form")
    browser["data[username]"] = username
    browser["data[displayname]"] = displayname
    browser["data[email]"] = "user@example.com"
    browser["data[password]"] = "password123"
    browser["data[passwordconfirm]"] = "password123"
    browser.submit_selected()

def navigate_to_user_addressbooks(browser: mechanicalsoup.StatefulBrowser, row_text="abuser"):
    follow_link_containing(browser, "users and resources")
    find_and_follow_row_link(browser, row_text, "address books")

def navigate_to_user_calendars(browser: mechanicalsoup.StatefulBrowser, row_text="abuser"):
    follow_link_containing(browser, "users and resources")
    find_and_follow_row_link(browser, row_text, "calendars")

def create_test_addressbook(browser: mechanicalsoup.StatefulBrowser, displayname="Test Address Book"):
    navigate_to_user_addressbooks(browser)
    follow_link_containing(browser, "add address book")
    browser.select_form("form")
    browser["data[uri]"] = "test-addressbook"
    browser["data[displayname]"] = displayname
    browser["data[description]"] = "A test address book"
    browser.submit_selected()

def create_test_calendar(browser: mechanicalsoup.StatefulBrowser, displayname="Test Calendar"):
    navigate_to_user_calendars(browser)
    follow_link_containing(browser, "add calendar")
    browser.select_form("form")
    browser["data[uri]"] = "test-calendar"
    browser["data[displayname]"] = displayname
    browser["data[description]"] = "A test calendar"
    browser.submit_selected()

def assert_payload_escaped(browser: mechanicalsoup.StatefulBrowser, context):
    raw_html = str(browser.get_current_page())
    assert XSS_PAYLOAD not in raw_html, f"XSS payload was reflected without HTML-escaping ({context})"

def test_addressbook_displayname_xss_is_escaped_on_create(browser: mechanicalsoup.StatefulBrowser):
    install_sqlite(browser)
    create_test_user(browser)

    create_test_addressbook(browser, displayname=XSS_PAYLOAD)
    # The edit form is shown right after creation, including the "has been created" notice.
    assert_payload_escaped(browser, "address book creation notice/edit form")

    # Confirm the payload is still escaped when the address book list is rendered.
    navigate_to_user_addressbooks(browser)
    assert_payload_escaped(browser, "address book list")

def test_addressbook_displayname_xss_is_escaped_on_edit(browser: mechanicalsoup.StatefulBrowser):
    install_sqlite(browser)
    create_test_user(browser)
    create_test_addressbook(browser)

    # The edit form is shown right after creation; update the display name to the payload.
    browser.select_form("form")
    browser["data[displayname]"] = XSS_PAYLOAD
    browser.submit_selected()
    # This triggers the "Changes ... have been saved" notice and the "Editing ..." title.
    assert_payload_escaped(browser, "address book edit notice/title")

    navigate_to_user_addressbooks(browser)
    assert_payload_escaped(browser, "address book list after edit")

def test_calendar_displayname_xss_is_escaped_on_create(browser: mechanicalsoup.StatefulBrowser):
    install_sqlite(browser)
    create_test_user(browser)

    create_test_calendar(browser, displayname=XSS_PAYLOAD)
    assert_payload_escaped(browser, "calendar creation notice/edit form")

    navigate_to_user_calendars(browser)
    assert_payload_escaped(browser, "calendar list")

def test_calendar_displayname_xss_is_escaped_on_edit(browser: mechanicalsoup.StatefulBrowser):
    install_sqlite(browser)
    create_test_user(browser)
    create_test_calendar(browser)

    browser.select_form("form")
    browser["data[displayname]"] = XSS_PAYLOAD
    browser.submit_selected()
    assert_payload_escaped(browser, "calendar edit notice/title")

    navigate_to_user_calendars(browser)
    assert_payload_escaped(browser, "calendar list after edit")

def test_user_username_xss_is_escaped_on_create(browser: mechanicalsoup.StatefulBrowser):
    install_sqlite(browser)
    create_test_user(browser, username=XSS_PAYLOAD, displayname="XSS User")
    # Username is the User model's LABELFIELD, used in the "has been created" notice.
    assert_payload_escaped(browser, "user creation notice/edit form")

    follow_link_containing(browser, "users and resources")
    assert_payload_escaped(browser, "user list")

def test_user_displayname_xss_is_escaped(browser: mechanicalsoup.StatefulBrowser):
    install_sqlite(browser)
    create_test_user(browser, username="dnuser", displayname=XSS_PAYLOAD)
    assert_payload_escaped(browser, "user creation notice/edit form")

    follow_link_containing(browser, "users and resources")
    assert_payload_escaped(browser, "user list")

def test_install_sqlite_path_xss_is_escaped(browser: mechanicalsoup.StatefulBrowser):
    setup_admin_password(browser)
    page = browser.get_current_page()
    assert "baïkal database setup" in page.text.lower()

    browser.select_form("form")
    browser["data[sqlite_file]"] = f"/nonexistent_dir_{XSS_PAYLOAD}/db.sqlite"
    browser.submit_selected()

    page = browser.get_current_page()
    assert "not writable" in page.text.lower()
    assert_payload_escaped(browser, "sqlite path validation error during install")

def test_settings_sqlite_path_xss_is_escaped(browser: mechanicalsoup.StatefulBrowser):
    install_sqlite(browser)

    follow_link_containing(browser, "database settings")
    browser.select_form("form")
    browser["data[sqlite_file]"] = f"/nonexistent_dir_{XSS_PAYLOAD}/db.sqlite"
    browser.submit_selected()

    page = browser.get_current_page()
    assert "not writable" in page.text.lower()
    assert_payload_escaped(browser, "sqlite path validation error in settings")
