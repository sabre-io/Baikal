import mechanicalsoup
from lib.backends import Backend
from lib.helpers import (
    follow_link_containing, follow_meta_redirect, find_and_follow_row_link,
)

def create_test_user(browser: mechanicalsoup.StatefulBrowser):
    follow_link_containing(browser, "users and resources")
    follow_link_containing(browser, "add user")
    browser.select_form("form")
    browser["data[username]"] = "testuser"
    browser["data[displayname]"] = "Test User"
    browser["data[email]"] = "testuser@example.com"
    browser["data[password]"] = "password123"
    browser["data[passwordconfirm]"] = "password123"
    browser.submit_selected()

def test_user_create(browser: mechanicalsoup.StatefulBrowser, backend: Backend):
    backend.install(browser)

    follow_link_containing(browser, "users and resources")
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

def test_user_create_missing_required_fields(browser: mechanicalsoup.StatefulBrowser, backend: Backend):
    backend.install(browser)

    follow_link_containing(browser, "users and resources")
    follow_link_containing(browser, "add user")
    browser.select_form("form")
    browser.submit_selected()

    page = browser.get_current_page()
    assert "validation error" in page.text.lower()
    assert "is required" in page.text.lower()

def test_user_create_invalid_email(browser: mechanicalsoup.StatefulBrowser, backend: Backend):
    backend.install(browser)

    follow_link_containing(browser, "users and resources")
    follow_link_containing(browser, "add user")
    browser.select_form("form")
    browser["data[username]"] = "testuser"
    browser["data[displayname]"] = "Test User"
    browser["data[email]"] = "notanemail"
    browser["data[password]"] = "password123"
    browser["data[passwordconfirm]"] = "password123"
    browser.submit_selected()

    page = browser.get_current_page()
    assert "validation error" in page.text.lower()
    assert "should be an email" in page.text.lower()

def test_user_create_password_mismatch(browser: mechanicalsoup.StatefulBrowser, backend: Backend):
    backend.install(browser)

    follow_link_containing(browser, "users and resources")
    follow_link_containing(browser, "add user")
    browser.select_form("form")
    browser["data[username]"] = "testuser"
    browser["data[displayname]"] = "Test User"
    browser["data[email]"] = "testuser@example.com"
    browser["data[password]"] = "password123"
    browser["data[passwordconfirm]"] = "differentpassword"
    browser.submit_selected()

    page = browser.get_current_page()
    assert "validation error" in page.text.lower()
    assert "does not match" in page.text.lower()

def test_user_create_duplicate_username(browser: mechanicalsoup.StatefulBrowser, backend: Backend):
    backend.install(browser)
    create_test_user(browser)

    follow_link_containing(browser, "add user")
    browser.select_form("form")
    browser["data[username]"] = "testuser"
    browser["data[displayname]"] = "Another User"
    browser["data[email]"] = "another@example.com"
    browser["data[password]"] = "password123"
    browser["data[passwordconfirm]"] = "password123"
    browser.submit_selected()

    page = browser.get_current_page()
    assert "validation error" in page.text.lower()
    assert "has to be unique" in page.text.lower()

def test_user_edit(browser: mechanicalsoup.StatefulBrowser, backend: Backend):
    backend.install(browser)
    create_test_user(browser)

    find_and_follow_row_link(browser, "testuser", "edit")
    browser.select_form("form")
    browser["data[displayname]"] = "Test User Updated"
    browser.submit_selected()

    page = browser.get_current_page()
    assert "test user updated" in page.text.lower()

def test_user_delete(browser: mechanicalsoup.StatefulBrowser, backend: Backend):
    backend.install(browser)
    create_test_user(browser)

    find_and_follow_row_link(browser, "testuser", "delete")
    follow_link_containing(browser, "delete testuser")
    follow_meta_redirect(browser)

    page = browser.get_current_page()
    assert "testuser" not in page.text
