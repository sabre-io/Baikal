import mechanicalsoup, os
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
    """Navigate to users list, create a new user and return to the users list page."""
    follow_link_containing(browser, "users and resources")
    follow_link_containing(browser, "add user")
    browser.select_form("form")
    browser["data[username]"] = username
    browser["data[displayname]"] = displayname
    browser["data[email]"] = email
    browser["data[password]"] = password
    browser["data[passwordconfirm]"] = password
    browser.submit_selected()

def test_system_settings(browser: mechanicalsoup.StatefulBrowser):
    install_sqlite(browser)

    # Navigate to System Settings
    follow_link_containing(browser, "system settings")
    page = browser.get_current_page()
    assert "baïkal system settings" in page.text.lower()

    # Submit the form after changing the timezone
    browser.select_form("form")
    browser["data[timezone]"] = "America/New_York"
    browser.submit_selected()

    # Verify still on settings page with no errors
    page = browser.get_current_page()
    assert "baïkal system settings" in page.text.lower()
    assert "error" not in page.text.lower()

def test_users_create_edit_delete(browser: mechanicalsoup.StatefulBrowser):
    install_sqlite(browser)

    # Navigate to users list and verify it loads
    follow_link_containing(browser, "users and resources")
    page = browser.get_current_page()
    assert "users" in page.text.lower()

    # Create a new user
    create_user(browser, "testuser", "Test User", "testuser@example.com", "password123")
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

def test_user_calendars(browser: mechanicalsoup.StatefulBrowser):
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

def test_user_addressbooks(browser: mechanicalsoup.StatefulBrowser):
    install_sqlite(browser)

    # Create a user and navigate to their address books
    create_user(browser, "abuser", "Address Book User", "abuser@example.com", "password123")
    find_and_follow_row_link(browser, "abuser", "address books")
    page = browser.get_current_page()
    assert "address books" in page.text.lower()
    assert "abuser" in page.text.lower()
    # Default address book should be present
    assert "default address book" in page.text.lower()

    # Add a new address book
    follow_link_containing(browser, "add address book")
    browser.select_form("form")
    browser["data[uri]"] = "test-addressbook"
    browser["data[displayname]"] = "Test Address Book"
    browser["data[description]"] = "A test address book"
    browser.submit_selected()
    page = browser.get_current_page()
    assert "test address book" in page.text.lower()

    # Edit the newly created address book (the form on the page already has the edit action)
    browser.select_form("form")
    browser["data[displayname]"] = "Test Address Book Updated"
    browser["data[description]"] = "Updated description"
    browser.submit_selected()
    page = browser.get_current_page()
    assert "test address book updated" in page.text.lower()

    # Navigate back to the address books page for a clean view
    follow_link_containing(browser, "back to users list")
    find_and_follow_row_link(browser, "abuser", "address books")

    # Delete "Test Address Book Updated"
    find_and_follow_row_link(browser, "test address book updated", "delete")
    # The delete confirmation page shows a "Delete Test Address Book Updated" link to confirm
    follow_link_containing(browser, "delete test address book updated")
    follow_meta_redirect(browser)

    # Verify address book is gone
    page = browser.get_current_page()
    assert "test address book updated" not in page.text.lower()
