CREATE TABLE calendarobjects ( 
	id INTEGER UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    calendardata MEDIUMBLOB, 
    uri VARCHAR(100), 
    calendarid INTEGER UNSIGNED NOT NULL, 
    lastmodified INT(11) 
);

CREATE TABLE calendars (
    id INTEGER UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    principaluri VARCHAR(100), 
    displayname VARCHAR(100), 
    uri VARCHAR(100), 
	ctag INTEGER UNSIGNED NOT NULL DEFAULT '0', 
    description TEXT, 
	calendarorder INTEGER UNSIGNED NOT NULL DEFAULT '0', 
    calendarcolor VARCHAR(10), 
	timezone TEXT, 
	components VARCHAR(20)
);
