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

def test_users_create_edit_delete(browser: mechanicalsoup.StatefulBrowser):
    install_sqlite(browser)

    # Navigate to users list and verify it loads
    follow_link_containing(browser, "users and resources")
    page = browser.get_current_page()
    assert "users" in page.text.lower()

    # Create a new user
    follow_link_containing(browser, "add user")
    browser.select_form("form")
    browser["data[username]"] = "testuser"
    browser["data[displayname]"] = "Test User"
    browser["data[email]"] = "testuser@example.com"
    browser["data[password]"] = "password123"
    browser["data[passwordconfirm]"] = "password123"
    browser.submit_selected()
    page = browser.get_current_page()
    assert "testuser" in page.text

    # Edit the user — navigate back to users list for a clean view, then edit
    follow_link_containing(browser, "users and resources")
    find_and_follow_row_link(browser, "testuser", "edit")
    browser.select_form("form")
    browser["data[displayname]"] = "Test User Updated"
    browser.submit_selected()
    page = browser.get_current_page()
    assert "test user updated" in page.text.lower()

    # Delete the user — navigate back to users list, then delete
    follow_link_containing(browser, "users and resources")
    find_and_follow_row_link(browser, "testuser", "delete")
    # The delete confirmation page shows a "Delete testuser" link to confirm
    follow_link_containing(browser, "delete testuser")
    follow_meta_redirect(browser)

    # Verify the user is no longer listed
    page = browser.get_current_page()
    assert "testuser" not in page.text
