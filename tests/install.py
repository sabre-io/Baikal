import mechanicalsoup
from test_helpers import install_sqlite, install_pgsql, install_mysql

def test_sqlite(browser: mechanicalsoup.StatefulBrowser):
    install_sqlite(browser)
    
def test_pgsql(browser: mechanicalsoup.StatefulBrowser):
    install_pgsql(browser)

def test_mysql(browser: mechanicalsoup.StatefulBrowser):
    install_mysql(browser)
