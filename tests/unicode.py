import mechanicalsoup
from lib import backends
from lib.helpers import follow_link_containing, find_and_follow_row_link

BACKENDS = backends.ALL

# 4-byte UTF-8 characters require utf8mb4 on MySQL
USER_DISPLAYNAME = "Emoji 👩‍💻 User"
CALENDAR_DISPLAYNAME = "Calendar 🎉"
CALENDAR_DESCRIPTION = "Party 🥳"
CALENDAR_DISPLAYNAME_UPDATED = "Calendar 👩‍💻 Updated"

def create_emoji_user(browser: mechanicalsoup.StatefulBrowser):
    follow_link_containing(browser, "users and resources")
    follow_link_containing(browser, "add user")
    browser.select_form("form")
    browser["data[username]"] = "emojiuser"
    browser["data[displayname]"] = USER_DISPLAYNAME
    browser["data[email]"] = "emojiuser@example.com"
    browser["data[password]"] = "password123"
    browser["data[passwordconfirm]"] = "password123"
    browser.submit_selected()

def navigate_to_user_calendars(browser: mechanicalsoup.StatefulBrowser):
    follow_link_containing(browser, "users and resources")
    find_and_follow_row_link(browser, "emojiuser", "calendars")

def test_emoji_user(browser: mechanicalsoup.StatefulBrowser, backend: backends.Backend):
    backend.install(browser)
    create_emoji_user(browser)

    follow_link_containing(browser, "users and resources")
    page = browser.get_current_page()
    assert USER_DISPLAYNAME in page.text

def test_emoji_calendar(browser: mechanicalsoup.StatefulBrowser, backend: backends.Backend):
    backend.install(browser)
    create_emoji_user(browser)

    navigate_to_user_calendars(browser)
    follow_link_containing(browser, "add calendar")
    browser.select_form("form")
    browser["data[uri]"] = "emoji-calendar"
    browser["data[displayname]"] = CALENDAR_DISPLAYNAME
    browser["data[description]"] = CALENDAR_DESCRIPTION
    browser.submit_selected()

    page = browser.get_current_page()
    assert CALENDAR_DISPLAYNAME in page.text
    assert CALENDAR_DESCRIPTION in page.text

    # The edit form is shown right after creation
    browser.select_form("form")
    browser["data[displayname]"] = CALENDAR_DISPLAYNAME_UPDATED
    browser.submit_selected()

    navigate_to_user_calendars(browser)
    page = browser.get_current_page()
    assert CALENDAR_DISPLAYNAME_UPDATED in page.text
    assert CALENDAR_DESCRIPTION in page.text
