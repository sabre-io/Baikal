<?php

abstract class Sabre_CardDAV_Backend_AbstractPDOTest extends PHPUnit_Framework_TestCase {

    protected $backend;

    abstract function getPDO();

    public function setUp() {

        $backend = new Sabre_CardDAV_Backend_PDO($this->getPDO());
        $this->backend = $backend;

    }    

    public function testGetAddressBooksForUser() {

        $result = $this->backend->getAddressBooksForUser('principals/user1');

        $expected = array(
            array(
                'id' => 1,
                'uri' => 'book1',
                'principaluri' => 'principals/user1',
                '{DAV:}displayname' => 'book1',
                '{' . Sabre_CardDAV_Plugin::NS_CARDDAV . '}addressbook-description' => 'addressbook 1',
                '{http://calendarserver.org/ns/}getctag' => 1,
                '{' . Sabre_CardDAV_Plugin::NS_CARDDAV . '}supported-address-data' => new Sabre_CardDAV_Property_SupportedAddressData(),
            )
        );

        $this->assertEquals($expected, $result);

    }

    public function testUpdateAddressBookInvalidProp() {

        $result = $this->backend->updateAddressBook(1, array(
            '{DAV:}displayname' => 'updated',
            '{' . Sabre_CardDAV_Plugin::NS_CARDDAV . '}addressbook-description' => 'updated',
            '{DAV:}foo' => 'bar',
        ));

        $this->assertFalse($result);

        $result = $this->backend->getAddressBooksForUser('principals/user1');

        $expected = array(
            array(
                'id' => 1,
                'uri' => 'book1',
                'principaluri' => 'principals/user1',
                '{DAV:}displayname' => 'book1',
                '{' . Sabre_CardDAV_Plugin::NS_CARDDAV . '}addressbook-description' => 'addressbook 1',
                '{http://calendarserver.org/ns/}getctag' => 1,
                '{' . Sabre_CardDAV_Plugin::NS_CARDDAV . '}supported-address-data' => new Sabre_CardDAV_Property_SupportedAddressData(),
            )
        );

        $this->assertEquals($expected, $result);
        

    }

    public function testUpdateAddressBookNoProps() {

        $result = $this->backend->updateAddressBook(1, array());

        $this->assertFalse($result);

        $result = $this->backend->getAddressBooksForUser('principals/user1');

        $expected = array(
            array(
                'id' => 1,
                'uri' => 'book1',
                'principaluri' => 'principals/user1',
                '{DAV:}displayname' => 'book1',
                '{' . Sabre_CardDAV_Plugin::NS_CARDDAV . '}addressbook-description' => 'addressbook 1',
                '{http://calendarserver.org/ns/}getctag' => 1,
                '{' . Sabre_CardDAV_Plugin::NS_CARDDAV . '}supported-address-data' => new Sabre_CardDAV_Property_SupportedAddressData(),
            )
        );

        $this->assertEquals($expected, $result);
        

    }

    public function testUpdateAddressBookSuccess() {

        $result = $this->backend->updateAddressBook(1, array(
            '{DAV:}displayname' => 'updated',
            '{' . Sabre_CardDAV_Plugin::NS_CARDDAV . '}addressbook-description' => 'updated',
        ));

        $this->assertTrue($result);

        $result = $this->backend->getAddressBooksForUser('principals/user1');

        $expected = array(
            array(
                'id' => 1,
                'uri' => 'book1',
                'principaluri' => 'principals/user1',
                '{DAV:}displayname' => 'updated',
                '{' . Sabre_CardDAV_Plugin::NS_CARDDAV . '}addressbook-description' => 'updated',
                '{http://calendarserver.org/ns/}getctag' => 2,
                '{' . Sabre_CardDAV_Plugin::NS_CARDDAV . '}supported-address-data' => new Sabre_CardDAV_Property_SupportedAddressData(),
            )
        );

        $this->assertEquals($expected, $result);
        

    }

    public function testDeleteAddressBook() {

        $this->backend->deleteAddressBook(1);

        $this->assertEquals(array(), $this->backend->getAddressBooksForUser('principals/user1'));

    }

    /**
     * @expectedException Sabre_DAV_Exception_BadRequest
     */
    public function testCreateAddressBookUnsupportedProp() {

        $this->backend->createAddressBook('principals/user1','book2', array(
            '{DAV:}foo' => 'bar',
        )); 

    }

    public function testCreateAddressBookSuccess() {

        $this->backend->createAddressBook('principals/user1','book2', array(
            '{DAV:}displayname' => 'book2',
            '{' . Sabre_CardDAV_Plugin::NS_CARDDAV . '}addressbook-description' => 'addressbook 2',
        )); 

        $expected = array(
            array(
                'id' => 1,
                'uri' => 'book1',
                'principaluri' => 'principals/user1',
                '{DAV:}displayname' => 'book1',
                '{' . Sabre_CardDAV_Plugin::NS_CARDDAV . '}addressbook-description' => 'addressbook 1',
                '{http://calendarserver.org/ns/}getctag' => 1,
                '{' . Sabre_CardDAV_Plugin::NS_CARDDAV . '}supported-address-data' => new Sabre_CardDAV_Property_SupportedAddressData(),
            ),
            array(
                'id' => 2,
                'uri' => 'book2',
                'principaluri' => 'principals/user1',
                '{DAV:}displayname' => 'book2',
                '{' . Sabre_CardDAV_Plugin::NS_CARDDAV . '}addressbook-description' => 'addressbook 2',
                '{http://calendarserver.org/ns/}getctag' => 1,
                '{' . Sabre_CardDAV_Plugin::NS_CARDDAV . '}supported-address-data' => new Sabre_CardDAV_Property_SupportedAddressData(),
            )
        );
        $result = $this->backend->getAddressBooksForUser('principals/user1');
        $this->assertEquals($expected, $result);

    }

    public function testGetCards() {

        $result = $this->backend->getCards(1);

        $expected = array(
            array(
                'id' => 1,
                'uri' => 'card1',
                'carddata' => 'card1',
                'lastmodified' => 0,
            )
        );

        $this->assertEquals($expected, $result);

    }    

    public function testGetCard() {

        $result = $this->backend->getCard(1,'card1');

        $expected = array(
            'id' => 1,
            'uri' => 'card1',
            'carddata' => 'card1',
            'lastmodified' => 0,
        );

        $this->assertEquals($expected, $result);

    }

    /**
     * @depends testGetCard
     */
    public function testCreateCard() {

        $this->backend->createCard(1, 'card2', 'data2');
        $result = $this->backend->getCard(1,'card2');
        $this->assertEquals(2, $result['id']);
        $this->assertEquals('card2', $result['uri']);
        $this->assertEquals('data2', $result['carddata']);

    } 

    /**
     * @depends testGetCard
     */
    public function testUpdateCard() {

        $this->backend->updateCard(1, 'card1', 'newdata');
        $result = $this->backend->getCard(1,'card1');
        $this->assertEquals(1, $result['id']);
        $this->assertEquals('newdata', $result['carddata']);

    }

    /**
     * @depends testGetCard
     */
    public function testDeleteCard() {

        $this->backend->deleteCard(1, 'card1');
        $result = $this->backend->getCard(1,'card1');
        $this->assertFalse($result);

    } 
}

