import mechanicalsoup, os
from lib import backends
from lib.helpers import ROOT_DIR, assert_upgrade

BACKENDS = backends.ALL

def test_install(browser: mechanicalsoup.StatefulBrowser, backend: backends.Backend):
    backend.install(browser)

def test_upgrade(browser: mechanicalsoup.StatefulBrowser, backend: backends.Backend):
    backend.install(browser) # Creates a proper database
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
    sqlite_file: {os.path.abspath(os.path.join(ROOT_DIR, "Specific/db/db.sqlite"))}
    {backend.config()}
    encryption_key: 89dc0abf3bf079f11df5f009a1b41fe1
"""
    with open(os.path.join(ROOT_DIR, "config/baikal.yaml"), "w", encoding="utf-8") as f:
        f.write(config)
    assert_upgrade(browser)
