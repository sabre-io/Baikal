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

def create_user(browser: mechanicalsoup.StatefulBrowser, username: str, displayname: str, email: str, password: str):
    """Navigate to users list, create a new user, and land on the users page."""
    follow_link_containing(browser, "users and resources")
    follow_link_containing(browser, "add user")
    browser.select_form("form")
    browser["data[username]"] = username
    browser["data[displayname]"] = displayname
    browser["data[email]"] = email
    browser["data[password]"] = password
    browser["data[passwordconfirm]"] = password
    browser.submit_selected()

def test_calendars_create_edit_delete(browser: mechanicalsoup.StatefulBrowser):
    install_sqlite(browser)

    # Create a user and navigate to their calendars
    create_user(browser, "caluser", "Calendar User", "caluser@example.com", "password123")
    find_and_follow_row_link(browser, "caluser", "calendars")
    page = browser.get_current_page()
    assert "calendars" in page.text.lower()
    assert "caluser" in page.text.lower()
    # Default calendar should be present
    assert "default calendar" in page.text.lower()

    # Add a new calendar
    follow_link_containing(browser, "add calendar")
    browser.select_form("form")
    browser["data[uri]"] = "test-calendar"
    browser["data[displayname]"] = "Test Calendar"
    browser["data[description]"] = "A test calendar"
    browser.submit_selected()
    page = browser.get_current_page()
    assert "test calendar" in page.text.lower()

    # Edit the newly created calendar (the form on the page already has the edit action)
    browser.select_form("form")
    browser["data[displayname]"] = "Test Calendar Updated"
    browser["data[description]"] = "Updated description"
    browser.submit_selected()
    page = browser.get_current_page()
    assert "test calendar updated" in page.text.lower()

    # Navigate back to the calendars page for a clean view
    follow_link_containing(browser, "back to users list")
    find_and_follow_row_link(browser, "caluser", "calendars")

    # Delete "Test Calendar Updated"
    find_and_follow_row_link(browser, "test calendar updated", "delete")
    # The delete confirmation page shows a "Delete Test Calendar Updated" link to confirm
    follow_link_containing(browser, "delete test calendar updated")
    follow_meta_redirect(browser)

    # Verify calendar is gone
    page = browser.get_current_page()
    assert "test calendar updated" not in page.text.lower()
