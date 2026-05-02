import mechanicalsoup
import os
from test_helpers import (
    assert_dashboard, assert_installed, setup_admin_password,
    follow_link_containing,
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

def test_system_settings_save(browser: mechanicalsoup.StatefulBrowser):
    install_sqlite(browser)

    follow_link_containing(browser, "system settings")
    page = browser.get_current_page()
    assert "baïkal system settings" in page.text.lower()

    browser.select_form("form")
    browser["data[timezone]"] = "America/New_York"
    browser.submit_selected()

    page = browser.get_current_page()
    assert "baïkal system settings" in page.text.lower()
    assert "error" not in page.text.lower()

def test_system_settings_password_mismatch(browser: mechanicalsoup.StatefulBrowser):
    install_sqlite(browser)

    follow_link_containing(browser, "system settings")
    browser.select_form("form")
    browser["data[admin_passwordhash]"] = "newpassword"
    browser["data[admin_passwordhash_confirm]"] = "differentpassword"
    browser.submit_selected()

    page = browser.get_current_page()
    assert "validation error" in page.text.lower()
    assert "does not match" in page.text.lower()
