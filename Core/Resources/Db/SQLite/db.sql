--
-- This is the empty database schema for Baïkal
-- Corresponds to the MySQL Schema definition of project SabreDAV 1.8.6
-- http://code.google.com/p/sabredav/
--

CREATE TABLE addressbooks (
    id integer primary key asc,
    principaluri text,
    displayname text,
    uri text,
    description text,
    ctag integer
);

CREATE TABLE cards (
    id integer primary key asc,
    addressbookid integer,
    carddata blob,
    uri text,
    lastmodified integer
);

CREATE TABLE calendarobjects (
    id integer primary key asc,
    calendardata blob,
    uri text,
    calendarid integer,
    lastmodified integer,
    etag text,
    size integer,
    componenttype text,
    firstoccurence integer,
    lastoccurence integer
);

CREATE TABLE calendars (
    id integer primary key asc,
    principaluri text,
    displayname text,
    uri text,
    ctag integer,
    description text,
    calendarorder integer,
    calendarcolor text,
    timezone text,
    components text,
    transparent bool
);

CREATE TABLE locks (
    id integer primary key asc,
    owner text,
    timeout integer,
    created integer,
    token text,
    scope integer,
    depth integer,
    uri text
);

CREATE TABLE principals (
    id INTEGER PRIMARY KEY ASC,
    uri TEXT,
    email TEXT,
    displayname TEXT,
    vcardurl TEXT,
    UNIQUE(uri)
);

CREATE TABLE groupmembers (
    id INTEGER PRIMARY KEY ASC,
    principal_id INTEGER,
    member_id INTEGER,
    UNIQUE(principal_id, member_id)
);

CREATE TABLE users (
    id integer primary key asc,
    username TEXT,
    digesta1 TEXT,
    UNIQUE(username)
);
