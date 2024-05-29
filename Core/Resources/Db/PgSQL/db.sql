
CREATE TABLE addressbooks (
    id SERIAL PRIMARY KEY,
    principaluri TEXT,
    displayname VARCHAR(255),
    uri TEXT,
    description TEXT,
    synctoken INT CHECK (synctoken > 0) NOT NULL DEFAULT '1'
);

CREATE TABLE cards (
    id SERIAL PRIMARY KEY,
    addressbookid INT CHECK (addressbookid > 0) NOT NULL,
    carddata TEXT,
    uri TEXT,
    lastmodified INT CHECK (lastmodified > 0),
    etag TEXT,
    size INT CHECK (size > 0) NOT NULL
);

CREATE TABLE addressbookchanges (
    id SERIAL PRIMARY KEY,
    uri TEXT NOT NULL,
    synctoken INT CHECK (synctoken > 0) NOT NULL,
    addressbookid INT CHECK (addressbookid > 0) NOT NULL,
    operation SMALLINT NOT NULL
);

CREATE INDEX addressbookid_synctoken ON addressbookchanges (addressbookid, synctoken);

CREATE TABLE calendarobjects (
    id SERIAL PRIMARY KEY,
    calendardata TEXT,
    uri TEXT,
    calendarid INTEGER CHECK (calendarid > 0) NOT NULL,
    lastmodified INT CHECK (lastmodified > 0),
    etag TEXT,
    size INT CHECK (size > 0) NOT NULL,
    componenttype TEXT,
    firstoccurence INT CHECK (firstoccurence > 0),
    lastoccurence INT CHECK (lastoccurence > 0),
    uid TEXT
);

CREATE TABLE calendars (
    id SERIAL PRIMARY KEY,
    synctoken INTEGER CHECK (synctoken > 0) NOT NULL DEFAULT '1',
    components TEXT
);

CREATE TABLE calendarinstances (
    id SERIAL PRIMARY KEY,
    calendarid INTEGER CHECK (calendarid > 0) NOT NULL,
    principaluri TEXT,
    access SMALLINT NOT NULL DEFAULT '1',
    displayname VARCHAR(100),
    uri TEXT,
    description TEXT,
    calendarorder INT CHECK (calendarorder >= 0) NOT NULL DEFAULT '0',
    calendarcolor TEXT,
    timezone TEXT,
    transparent SMALLINT NOT NULL DEFAULT '0',
    share_href TEXT,
    share_displayname VARCHAR(100),
    share_invitestatus SMALLINT NOT NULL DEFAULT '2'
);

CREATE TABLE calendarchanges (
    id SERIAL PRIMARY KEY,
    uri TEXT NOT NULL,
    synctoken INT CHECK (synctoken > 0) NOT NULL,
    calendarid INT CHECK (calendarid > 0) NOT NULL,
    operation SMALLINT NOT NULL
);

CREATE INDEX calendarid_synctoken ON calendarchanges (calendarid, synctoken);

CREATE TABLE calendarsubscriptions (
    id SERIAL PRIMARY KEY,
    uri TEXT NOT NULL,
    principaluri TEXT NOT NULL,
    source TEXT,
    displayname VARCHAR(100),
    refreshrate VARCHAR(10),
    calendarorder INT CHECK (calendarorder >= 0) NOT NULL DEFAULT '0',
    calendarcolor TEXT,
    striptodos SMALLINT NULL,
    stripalarms SMALLINT NULL,
    stripattachments SMALLINT NULL,
    lastmodified INT CHECK (lastmodified > 0)
);

CREATE TABLE schedulingobjects (
    id SERIAL PRIMARY KEY,
    principaluri TEXT,
    calendardata TEXT,
    uri TEXT,
    lastmodified INT CHECK (lastmodified > 0),
    etag TEXT,
    size INT CHECK (size > 0) NOT NULL
);
CREATE TABLE locks (
    id SERIAL PRIMARY KEY,
    owner VARCHAR(100),
    timeout INTEGER CHECK (timeout > 0),
    created INTEGER,
    token TEXT,
    scope SMALLINT,
    depth SMALLINT,
    uri TEXT
);

CREATE INDEX ON locks (token);
CREATE INDEX ON locks (uri);

CREATE TABLE principals (
    id SERIAL PRIMARY KEY,
    uri TEXT NOT NULL,
    email TEXT,
    displayname VARCHAR(80)
);

CREATE TABLE groupmembers (
    id SERIAL PRIMARY KEY,
    principal_id INTEGER CHECK (principal_id > 0) NOT NULL,
    member_id INTEGER CHECK (member_id > 0) NOT NULL
);

CREATE TABLE propertystorage (
    id SERIAL PRIMARY KEY,
    path TEXT NOT NULL,
    name TEXT NOT NULL,
    valuetype INT CHECK (valuetype > 0),
    value TEXT
);

CREATE UNIQUE INDEX path_property ON propertystorage (path, name);
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    username TEXT,
    digesta1 TEXT
);
