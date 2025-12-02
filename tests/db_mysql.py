import mechanicalsoup, os
import importlib.util, mysql.connector
from test_helpers import assert_installed, assert_dashboard, assert_upgrade, setup_admin_password

def setup():
    conn = mysql.connector.connect(
        host="127.0.0.1",
        user="baikal",
        password="baikal"
    )
    cursor = conn.cursor()
    cursor.execute("DROP DATABASE IF EXISTS baikal_test")
    cursor.execute("CREATE DATABASE baikal_test")
    conn.commit()
    cursor.close()
    conn.close()

def install_mysql(browser: mechanicalsoup.StatefulBrowser):
    setup_admin_password(browser)

    page = browser.get_current_page()
    assert "baïkal database setup" in page.text.lower()
    browser.select_form("form")
    browser["data[backend]"] = "mysql"
    browser.submit_selected()

    page = browser.get_current_page()
    assert "mysql host" in page.text.lower()
    browser.select_form("form")
    browser["data[mysql_host]"] = "127.0.0.1"
    browser["data[mysql_dbname]"] = "baikal_test"
    browser["data[mysql_username]"] = "baikal"
    browser["data[mysql_password]"] = "baikal"
    browser.submit_selected()

    assert_installed(browser)
    assert_dashboard(browser)

def test_install(browser: mechanicalsoup.StatefulBrowser):
    install_mysql(browser)

def test_upgrade(browser: mechanicalsoup.StatefulBrowser):
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
