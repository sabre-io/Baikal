import mechanicalsoup, os, psycopg2
from test_helpers import assert_installed, assert_dashboard, assert_upgrade, setup_admin_password

def setup():
    conn = psycopg2.connect(
        host="127.0.0.1",
        user="baikal",
        password="baikal",
        dbname="postgres"
    )
    conn.autocommit = True
    cursor = conn.cursor()
    cursor.execute("DROP DATABASE IF EXISTS baikal_test")
    cursor.execute("CREATE DATABASE baikal_test")
    cursor.close()
    conn.close()

def install_pgsql(browser: mechanicalsoup.StatefulBrowser):
    setup_admin_password(browser)

    page = browser.get_current_page()
    assert "baïkal database setup" in page.text.lower()
    browser.select_form("form")
    browser["data[backend]"] = "pgsql"
    browser.submit_selected()

    page = browser.get_current_page()
    assert "postgresql host" in page.text.lower()
    browser.select_form("form")
    browser["data[pgsql_host]"] = "127.0.0.1"
    browser["data[pgsql_dbname]"] = "baikal_test"
    browser["data[pgsql_username]"] = "baikal"
    browser["data[pgsql_password]"] = "baikal"
    browser.submit_selected()

    assert_installed(browser)
    assert_dashboard(browser)

def test_install(browser: mechanicalsoup.StatefulBrowser):
    install_pgsql(browser)

def test_upgrade(browser: mechanicalsoup.StatefulBrowser):
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
    backend: pgsql
    pgsql_host: 127.0.0.1
    pgsql_dbname: baikal_test
    pgsql_username: baikal
    pgsql_password: baikal
    encryption_key: 89dc0abf3bf079f11df5f009a1b41fe1
"""
    with open("config/baikal.yaml", "w", encoding="utf-8") as f:
        f.write(config)
    assert_upgrade(browser)

        
