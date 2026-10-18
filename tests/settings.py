import mechanicalsoup
from lib.backends import Backend
from lib.helpers import (
    follow_link_containing,
)

def test_system_settings_save(browser: mechanicalsoup.StatefulBrowser, backend: Backend):
    backend.install(browser)

    follow_link_containing(browser, "system settings")
    page = browser.get_current_page()
    assert "baïkal system settings" in page.text.lower()

    browser.select_form("form")
    browser["data[timezone]"] = "America/New_York"
    browser.submit_selected()

    page = browser.get_current_page()
    assert "baïkal system settings" in page.text.lower()
    assert "error" not in page.text.lower()

def test_system_settings_password_mismatch(browser: mechanicalsoup.StatefulBrowser, backend: Backend):
    backend.install(browser)

    follow_link_containing(browser, "system settings")
    browser.select_form("form")
    browser["data[admin_passwordhash]"] = "newpassword"
    browser["data[admin_passwordhash_confirm]"] = "differentpassword"
    browser.submit_selected()

    page = browser.get_current_page()
    assert "validation error" in page.text.lower()
    assert "does not match" in page.text.lower()
