import mechanicalsoup
import os
import requests
import socket
import threading
from urllib.parse import urljoin
from test_helpers import (
    BASE_URL,
    assert_installed, setup_admin_password, follow_link_containing
)

def setup():
    db_path = "Specific/db/db.sqlite"
    if os.path.exists(db_path):
        os.remove(db_path)

class MockIMAPServer(threading.Thread):
    def __init__(self, port, expected_user, expected_pass):
        super().__init__()
        self.port = port
        self.expected_user = expected_user
        self.expected_pass = expected_pass
        self.server_socket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
        self.server_socket.setsockopt(socket.SOL_SOCKET, socket.SO_REUSEADDR, 1)
        self.server_socket.bind(('127.0.0.1', self.port))
        self.server_socket.listen(1)
        self.daemon = True

    def run(self):
        try:
            while True:
                client, addr = self.server_socket.accept()
                client.sendall(b"* OK IMAP4rev1 Service Ready\r\n")
                while True:
                    data = client.recv(1024)
                    if not data:
                        break
                    line = data.decode('utf-8', errors='replace').strip()
                    if 'LOGIN' in line:
                        parts = line.split(' ', 3)
                        # IMAP LOGIN format: TAG LOGIN username password
                        if len(parts) >= 4 and parts[2].strip('"') == self.expected_user and parts[3].strip('"') == self.expected_pass:
                            client.sendall(parts[0].encode() + b" OK LOGIN completed\r\n")
                        else:
                            client.sendall(parts[0].encode() + b" NO LOGIN failed\r\n")
                    elif 'LOGOUT' in line:
                        parts = line.split(' ', 1)
                        client.sendall(b"* BYE IMAP4rev1 Server logging out\r\n")
                        client.sendall(parts[0].encode() + b" OK LOGOUT completed\r\n")
                        break
                    else:
                        parts = line.split(' ', 1)
                        client.sendall(parts[0].encode() + b" OK command ignored\r\n")
                client.close()
        except Exception:
            pass

def install_sqlite(browser: mechanicalsoup.StatefulBrowser):
    setup_admin_password(browser)
    page = browser.get_current_page()
    assert "baïkal database setup" in page.text.lower()
    browser.select_form("form")
    browser.submit_selected()
    assert_installed(browser)

def test_imap_authentication(browser: mechanicalsoup.StatefulBrowser):
    install_sqlite(browser)

    # Start mock IMAP server
    imap_port = 11143
    imap_user = "admin"
    imap_pass = "imap_secret_123"
    mock_imap = MockIMAPServer(imap_port, imap_user, imap_pass)
    mock_imap.start()

    # Configure Baikal to use IMAP
    follow_link_containing(browser, "system settings")
    browser.select_form("form")
    browser["data[dav_auth_type]"] = "IMAP"
    browser.submit_selected()

    browser.select_form("form")
    browser["data[imap_connection]"] = f"127.0.0.1:{imap_port}/imap/novalidate-cert"
    browser.submit_selected()

    # Test WebDAV authentication against Baikal
    dav_url = urljoin(BASE_URL, 'cal.php/principals/')
    
    # Wait briefly for server to be fully responsive
    import time
    time.sleep(1)
    
    # 1. Incorrect password
    response = requests.request('PROPFIND', dav_url, auth=(imap_user, 'wrong_pass'))
    assert response.status_code == 401, f"Expected 401, got {response.status_code}"

    # 2. Correct password
    response = requests.request('PROPFIND', dav_url, auth=(imap_user, imap_pass))
    assert response.status_code in (200, 207), f"Expected 207 Multi-Status, got {response.status_code}"
