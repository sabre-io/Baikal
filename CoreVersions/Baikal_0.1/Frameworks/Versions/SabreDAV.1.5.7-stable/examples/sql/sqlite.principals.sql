CREATE TABLE principals (
	id INTEGER PRIMARY KEY ASC, 
	uri TEXT,
	email TEXT,
	displayname TEXT,
	UNIQUE(uri)
);

CREATE TABLE groupmembers (
	id INTEGER PRIMARY KEY ASC,
	principal_id INTEGER,
	member_id INTEGER,
	UNIQUE(principal_id, member_id)
);
	

INSERT INTO principals (uri,email,displayname) VALUES ('principals/admin', 'admin@example.org','Adminstrator');
INSERT INTO principals (uri,email,displayname) VALUES ('principals/admin/calendar-proxy-read', null, null);
INSERT INTO principals (uri,email,displayname) VALUES ('principals/admin/calendar-proxy-write', null, null);

