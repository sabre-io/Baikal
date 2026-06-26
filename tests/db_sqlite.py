import mechanicalsoup, os
from test_helpers import assert_installed, assert_dashboard, assert_upgrade, setup_admin_password

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

def test_install(browser: mechanicalsoup.StatefulBrowser):
    install_sqlite(browser)
    
def test_upgrade(browser: mechanicalsoup.StatefulBrowser):
    install_sqlite(browser) # Creates a proper database
    config = f"""
system:
    configured_version: 0.10.1
    timezone: Europe/Paris
    card_enabled: true
    cal_enabled: true
    dav_auth_type: Digest
    admin_passwordhash: abbc0ffed774e98a495fe5a2596ed7deab14211f250673ad661be7b759c9a7fd
    failed_access_message: 'user %u authentication failure for Baikal'
    auth_realm: BaikalDAV
    base_uri: ''
    invite_from: noreply@localhost
database:
    sqlite_file: {os.getcwd()}/Specific/db/db.sqlite
    backend: sqlite
    encryption_key: 89dc0abf3bf079f11df5f009a1b41fe1
"""
    with open("config/baikal.yaml", "w", encoding="utf-8") as f:
        f.write(config)
    assert_upgrade(browser)
