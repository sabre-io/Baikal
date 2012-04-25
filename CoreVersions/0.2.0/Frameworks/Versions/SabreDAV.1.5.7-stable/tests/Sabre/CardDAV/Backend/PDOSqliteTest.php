<?php

require_once 'Sabre/TestUtil.php';

class Sabre_CardDAV_Backend_PDOSqliteTest extends Sabre_CardDAV_Backend_AbstractPDOTest {

    function tearDown() {

        if (file_exists(SABRE_TEMPDIR . '/pdobackend')) unlink(SABRE_TEMPDIR . '/pdobackend');
        if (file_exists(SABRE_TEMPDIR . '/pdobackend2')) unlink(SABRE_TEMPDIR . '/pdobackend2');

    }

    function getPDO() {

        if (!SABRE_HASSQLITE) $this->markTestSkipped('SQLite driver is not available');
        $pdo = new PDO('sqlite:'.SABRE_TEMPDIR.'/pdobackend');
        $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

        $pdo->query("DROP TABLE IF EXISTS addressbooks");
        $pdo->query("DROP TABLE IF EXISTS cards");
        $pdo->query("
CREATE TABLE addressbooks (
    id integer primary key asc, 
    principaluri text, 
    displayname text, 
    uri text,
    description text,
	ctag integer
);

");

        $pdo->query("
INSERT INTO addressbooks
    (principaluri, displayname, uri, description, ctag)
VALUES
    ('principals/user1', 'book1', 'book1', 'addressbook 1', 1);
");

        $pdo->query("

CREATE TABLE cards ( 
	id integer primary key asc, 
    addressbookid integer, 
    carddata text, 
    uri text, 
    lastmodified integer
);

");
        $pdo->query("
INSERT INTO cards
    (addressbookid, carddata, uri, lastmodified)
VALUES
    (1, 'card1', 'card1', 0);
");

        return $pdo;

    }

}

