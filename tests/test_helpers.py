import mechanicalsoup, os, sys

BASE_URL = os.environ.get("BAIKAL_BASE_URL", "http://localhost/html/")
ADMIN_PASSWORD = "secret123"

def follow_link_containing(browser: mechanicalsoup.StatefulBrowser, text_substring: str):
    text_substring = text_substring.lower()
    page = browser.get_current_page()
    link = None
    for a in page.find_all("a"):
        if a and a.get_text(strip=True) and text_substring in a.get_text(strip=True).lower():
            link = a
            break
    if link is None:
        raise RuntimeError(f"No link containing '{text_substring}' found on page")
    browser.follow_link(link)

def setup_admin_password(browser: mechanicalsoup.StatefulBrowser):
    browser.open(BASE_URL)
    page = browser.get_current_page()
    assert "baïkal initialization wizard" in page.text.lower()
    browser.select_form("form")
    browser["data[admin_passwordhash]"] = ADMIN_PASSWORD
    browser["data[admin_passwordhash_confirm]"] = ADMIN_PASSWORD
    browser.submit_selected()

def assert_installed(browser: mechanicalsoup.StatefulBrowser):
    # Confirmation page
    page = browser.get_current_page()
    assert "baïkal is now installed, and its database properly configured" in page.text.lower()

    # Landing page
    browser.open(BASE_URL)
    page = browser.get_current_page()
    assert "baïkal is running alright" in page.text.lower()
    follow_link_containing(browser, "login")

    # Login page
    browser.select_form("form")
    browser["login"] = "admin"
    browser["password"] = ADMIN_PASSWORD
    browser.submit_selected()

def assert_dashboard(browser: mechanicalsoup.StatefulBrowser):
    page = browser.get_current_page()
    assert "dashboard" in page.text.lower()
    assert "about this system" in page.text.lower()

def assert_upgrade(browser: mechanicalsoup.StatefulBrowser):
    browser.open(BASE_URL)
    page = browser.get_current_page()
    assert "baïkal upgrade wizard" in page.text.lower()
    browser.follow_link(text="Start upgrade")
    page = browser.get_current_page()
    assert "baïkal has been successfully upgraded" in page.text.lower()
    browser.follow_link(text="Access the Baïkal admin")
    assert_dashboard(browser)
