# What is SabreDAV

SabreDAV allows you to easily add WebDAV support to a PHP application. SabreDAV is meant to cover the entire standard, and attempts to allow integration using an easy to understand API.

### Feature list:

* Fully WebDAV compliant
* Supports Windows XP, Windows Vista, Mac OS/X, DavFSv2, Cadaver, Netdrive, Open Office, and probably more.
* Passing all Litmus tests.
* Supporting class 1, 2 and 3 Webdav servers.
* Locking support.
* Custom property support.
* CalDAV (tested with [Evolution](http://code.google.com/p/sabredav/wiki/Evolution), [iCal](http://code.google.com/p/sabredav/wiki/ICal), [iPhone](http://code.google.com/p/sabredav/wiki/IPhone) and [Lightning](http://code.google.com/p/sabredav/wiki/Lightning)).
* CardDAV (tested with [OS/X addressbook](http://code.google.com/p/sabredav/wiki/OSXAddressbook), the [iOS addressbook](http://code.google.com/p/sabredav/wiki/iOSCardDAV) and [Evolution](http://code.google.com/p/sabredav/wiki/Evolution)).
* Over 97% unittest code coverage.

### Supported RFC's:

* [RFC2617](http://www.ietf.org/rfc/rfc2617.txt): Basic/Digest auth.
* [RFC2518](http://www.ietf.org/rfc/rfc2518.txt): First WebDAV spec.
* [RFC3744](http://www.ietf.org/rfc/rfc3744.txt): ACL (some features missing).
* [RFC4709](http://www.ietf.org/rfc/rfc4709.txt): [DavMount](http://code.google.com/p/sabredav/wiki/DavMount).
* [RFC4791](http://www.ietf.org/rfc/rfc4791.txt): CalDAV.
* [RFC4918](http://www.ietf.org/rfc/rfc4918.txt): WebDAV revision.
* [RFC5397](http://www.ietf.org/rfc/rfc5689.txt): current-user-principal.
* [RFC5689](http://www.ietf.org/rfc/rfc5689.txt): Extended MKCOL.
* [RFC5789](http://tools.ietf.org/html/rfc5789): PATCH method for HTTP.
* [RFC6352](http://www.ietf.org/rfc/rfc6352.txt): CardDAV
* [draft-daboo-carddav-directory-gateway](http://tools.ietf.org/html/draft-daboo-carddav-directory-gateway): CardDAV directory gateway
* CalDAV ctag, CalDAV-proxy.
