import mechanicalsoup, os
from test_helpers import install_sqlite, install_pgsql, install_mysql, assert_dashboard, BASE_URL

def assert_upgrade(browser: mechanicalsoup.StatefulBrowser):
    browser.open(BASE_URL)
    page = browser.get_current_page()
    assert "baïkal upgrade wizard" in page.text.lower()
    browser.follow_link(text="Start upgrade")
    page = browser.get_current_page()
    assert "baïkal has been successfully upgraded" in page.text.lower()
    browser.follow_link(text="Access the Baïkal admin")
    assert_dashboard(browser)

def test_sqlite(browser: mechanicalsoup.StatefulBrowser):
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
    
def ignored_test_pgsql(browser: mechanicalsoup.StatefulBrowser):
    install_pgsql(browser) # Creates a proper database
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
    backend: postgres
    pgsql_host: 127.0.0.1
    pgsql_dbname: baikal_test
    pgsql_username: baikal
    pgsql_password: baikal
    encryption_key: 89dc0abf3bf079f11df5f009a1b41fe1
"""
    with open("config/baikal.yaml", "w", encoding="utf-8") as f:
        f.write(config)
    assert_upgrade(browser)
        
def test_mysql(browser: mechanicalsoup.StatefulBrowser):
    install_mysql(browser) # Creates a proper database
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
    backend: mysql
    mysql_host: 127.0.0.1
    mysql_dbname: baikal_test
    mysql_username: baikal
    mysql_password: baikal
    encryption_key: 89dc0abf3bf079f11df5f009a1b41fe1
"""
    with open("config/baikal.yaml", "w", encoding="utf-8") as f:
        f.write(config)
    assert_upgrade(browser)
        
