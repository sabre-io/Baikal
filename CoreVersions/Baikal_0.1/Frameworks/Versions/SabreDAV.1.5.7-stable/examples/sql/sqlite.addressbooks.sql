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

