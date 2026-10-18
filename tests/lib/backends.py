import mechanicalsoup, os
from lib.helpers import ROOT_DIR, assert_installed, assert_dashboard, setup_admin_password

MYSQL_HOST = os.environ.get("BAIKAL_TEST_MYSQL_HOST", "127.0.0.1")
PGSQL_HOST = os.environ.get("BAIKAL_TEST_PGSQL_HOST", "127.0.0.1")

class Backend:
    name = None

    def reset(self):
        raise NotImplementedError

    def fill_install_form(self, browser: mechanicalsoup.StatefulBrowser):
        raise NotImplementedError

    def config(self) -> str:
        raise NotImplementedError

    def install(self, browser: mechanicalsoup.StatefulBrowser):
        setup_admin_password(browser)

        page = browser.get_current_page()
        assert "baïkal database setup" in page.text.lower()
        self.fill_install_form(browser)

        assert_installed(browser)
        assert_dashboard(browser)

    def __str__(self):
        return self.name

class Sqlite(Backend):
    name = "sqlite"

    def reset(self):
        db_path = os.path.join(ROOT_DIR, "Specific/db/db.sqlite")
        if os.path.exists(db_path):
            os.remove(db_path)

    def fill_install_form(self, browser):
        browser.select_form("form")
        browser.submit_selected()

    def config(self):
        return "backend: sqlite"

class Mysql(Backend):
    name = "mysql"

    def reset(self):
        import mysql.connector
        conn = mysql.connector.connect(
            host=MYSQL_HOST,
            user="baikal",
            password="baikal"
        )
        cursor = conn.cursor()
        cursor.execute("DROP DATABASE IF EXISTS baikal_test")
        cursor.execute("CREATE DATABASE baikal_test")
        conn.commit()
        cursor.close()
        conn.close()

    def fill_install_form(self, browser):
        browser.select_form("form")
        browser["data[backend]"] = "mysql"
        browser.submit_selected()

        page = browser.get_current_page()
        assert "mysql host" in page.text.lower()
        browser.select_form("form")
        browser["data[mysql_host]"] = MYSQL_HOST
        browser["data[mysql_dbname]"] = "baikal_test"
        browser["data[mysql_username]"] = "baikal"
        browser["data[mysql_password]"] = "baikal"
        browser.submit_selected()

    def config(self):
        return f"""backend: mysql
    mysql_host: {MYSQL_HOST}
    mysql_dbname: baikal_test
    mysql_username: baikal
    mysql_password: baikal"""

class Pgsql(Backend):
    name = "pgsql"

    def reset(self):
        import psycopg2
        conn = psycopg2.connect(
            host=PGSQL_HOST,
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

    def fill_install_form(self, browser):
        browser.select_form("form")
        browser["data[backend]"] = "pgsql"
        browser.submit_selected()

        page = browser.get_current_page()
        assert "postgresql host" in page.text.lower()
        browser.select_form("form")
        browser["data[pgsql_host]"] = PGSQL_HOST
        browser["data[pgsql_dbname]"] = "baikal_test"
        browser["data[pgsql_username]"] = "baikal"
        browser["data[pgsql_password]"] = "baikal"
        browser.submit_selected()

    def config(self):
        return f"""backend: pgsql
    pgsql_host: {PGSQL_HOST}
    pgsql_dbname: baikal_test
    pgsql_username: baikal
    pgsql_password: baikal"""

SQLITE = Sqlite()
ALL = [SQLITE, Mysql(), Pgsql()]
