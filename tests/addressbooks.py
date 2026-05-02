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

def test_addressbooks_create_edit_delete(browser: mechanicalsoup.StatefulBrowser):
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
