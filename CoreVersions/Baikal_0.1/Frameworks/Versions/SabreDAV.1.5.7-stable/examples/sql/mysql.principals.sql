CREATE TABLE principals (
	id INTEGER UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
	uri VARCHAR(100) NOT NULL,
	email VARCHAR(80),
	displayname VARCHAR(80),
	UNIQUE(uri)
);

CREATE TABLE groupmembers (
	id INTEGER UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
	principal_id INTEGER UNSIGNED NOT NULL,
	member_id INTEGER UNSIGNED NOT NULL,
	UNIQUE(principal_id, member_id)
);
	

INSERT INTO principals (uri,email,displayname) VALUES
('principals/admin', 'admin@example.org','Adminstrator'),
('principals/admin/calendar-proxy-read', null, null),
('principals/admin/calendar-proxy-write', null, null);

