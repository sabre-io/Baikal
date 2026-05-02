import mechanicalsoup
import os
from test_helpers import (
    assert_dashboard, assert_installed, setup_admin_password,
    follow_link_containing, follow_meta_redirect, find_and_follow_row_link,
)

def setup():
    db_path = "Specific/db/db.sqlite"
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

def create_test_user(browser: mechanicalsoup.StatefulBrowser):
    """Create a standard test user. install_sqlite must have been called first."""
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
    """Navigate to the calendar list for caluser."""
    follow_link_containing(browser, "users and resources")
    find_and_follow_row_link(browser, "caluser", "calendars")

def create_test_calendar(browser: mechanicalsoup.StatefulBrowser):
    """Create a test calendar for caluser. install_sqlite and create_test_user must have been called first."""
    navigate_to_user_calendars(browser)
    follow_link_containing(browser, "add calendar")
    browser.select_form("form")
    browser["data[uri]"] = "test-calendar"
    browser["data[displayname]"] = "Test Calendar"
    browser["data[description]"] = "A test calendar"
    browser.submit_selected()

def test_calendar_create(browser: mechanicalsoup.StatefulBrowser):
    install_sqlite(browser)
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

def test_calendar_create_invalid_uri(browser: mechanicalsoup.StatefulBrowser):
    install_sqlite(browser)
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

def test_calendar_create_missing_displayname(browser: mechanicalsoup.StatefulBrowser):
    install_sqlite(browser)
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

def test_calendar_edit(browser: mechanicalsoup.StatefulBrowser):
    install_sqlite(browser)
    create_test_user(browser)
    create_test_calendar(browser)

    # The edit form is shown right after creation; update the display name
    browser.select_form("form")
    browser["data[displayname]"] = "Test Calendar Updated"
    browser["data[description]"] = "Updated description"
    browser.submit_selected()

    page = browser.get_current_page()
    assert "test calendar updated" in page.text.lower()

def test_calendar_delete(browser: mechanicalsoup.StatefulBrowser):
    install_sqlite(browser)
    create_test_user(browser)
    create_test_calendar(browser)

    navigate_to_user_calendars(browser)
    find_and_follow_row_link(browser, "test calendar", "delete")
    follow_link_containing(browser, "delete test calendar")
    follow_meta_redirect(browser)

    page = browser.get_current_page()
    assert "test calendar" not in page.text.lower()
