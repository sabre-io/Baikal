import mechanicalsoup
import os
import sys
from urllib.parse import urljoin

BASE_URL = os.environ.get("BAIKAL_BASE_URL", "http://localhost/html/")
ADMIN_PASSWORD = "secret123"

def follow_link_containing(browser: mechanicalsoup.StatefulBrowser, text_substring: str):
    text_substring = text_substring.lower()
    page = browser.get_current_page()
    link = None
    for a in page.find_all("a"):
        # Normalize whitespace so that links whose text spans multiple nested
        # tags (e.g. "Delete <strong><i></i> username</strong>") are matched
        # reliably by their plain-text content.
        text = " ".join(a.get_text().split()).lower()
        if text and text_substring in text:
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

def follow_meta_redirect(browser: mechanicalsoup.StatefulBrowser):
    """Navigate to the URL specified in a meta-refresh tag, if present.

    If the current page contains a meta-refresh tag, the browser navigates to
    the target URL. If no such tag is found, this is a no-op. Relative URLs
    are resolved against the current page URL.
    """
    page = browser.get_current_page()
    meta = page.find("meta", attrs={"http-equiv": lambda x: x and x.lower() == "refresh"})
    if meta:
        content = meta.get("content", "")
        if "url=" in content.lower():
            idx = content.lower().index("url=")
            url = content[idx + 4:].strip()
            url = urljoin(browser.get_url(), url)
            browser.open(url)

def find_and_follow_row_link(browser: mechanicalsoup.StatefulBrowser, row_text: str, link_text: str):
    """Follow a link inside a table row that contains row_text.

    Scans all <tr> elements on the current page for one whose text content
    contains row_text (case-insensitive), then follows the first <a> element
    within that row whose text contains link_text (case-insensitive).

    Raises RuntimeError if no matching row or link is found.
    """
    page = browser.get_current_page()
    row_text_lower = row_text.lower()
    link_text_lower = link_text.lower()
    for tr in page.find_all("tr"):
        if row_text_lower in tr.get_text(strip=True).lower():
            for a in tr.find_all("a"):
                if a.get_text(strip=True) and link_text_lower in a.get_text(strip=True).lower():
                    browser.follow_link(a)
                    return
    raise RuntimeError(f"No row containing '{row_text}' with link '{link_text}' found on page")

def assert_upgrade(browser: mechanicalsoup.StatefulBrowser):
    browser.open(BASE_URL)
    page = browser.get_current_page()
    assert "baïkal upgrade wizard" in page.text.lower()
    browser.follow_link(text="Start upgrade")
    page = browser.get_current_page()
    assert "baïkal has been successfully upgraded" in page.text.lower()
    browser.follow_link(text="Access the Baïkal admin")
    assert_dashboard(browser)
