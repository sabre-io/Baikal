<?php

require_once 'Sabre/CardDAV/MockBackend.php';

class Sabre_CardDAV_AddressBookTest extends PHPUnit_Framework_TestCase {

    protected $ab;
    protected $backend;

    function setUp() {

        $this->backend = new Sabre_CardDAV_MockBackend();
        $this->ab = new Sabre_CardDAV_AddressBook(
            $this->backend,
            array(
                'uri' => 'book1',
                'id' => 'foo',
                '{DAV:}displayname' => 'd-name',
                'principaluri' => 'principals/user1',
            )
        );

    }

    function testGetName() {

        $this->assertEquals('book1', $this->ab->getName());

    }

    function testGetChild() {

        $card = $this->ab->getChild('card1');
        $this->assertInstanceOf('Sabre_CardDAV_Card', $card);
        $this->assertEquals('card1', $card->getName()); 

    }

    /**
     * @expectedException Sabre_DAV_Exception_FileNotFound
     */
    function testGetChildNotFound() {

        $card = $this->ab->getChild('card3');

    }

    function testGetChildren() {

        $cards = $this->ab->getChildren();
        $this->assertEquals(2, count($cards));

        $this->assertEquals('card1', $cards[0]->getName());
        $this->assertEquals('card2', $cards[1]->getName());

    } 

    /**
     * @expectedException Sabre_DAV_Exception_MethodNotAllowed
     */
    function testCreateDirectory() {

        $this->ab->createDirectory('name');

    }

    function testCreateFile() {

        $file = fopen('php://memory','r+');
        fwrite($file,'foo');
        rewind($file);
        $this->ab->createFile('card2',$file);

        $this->assertEquals('foo', $this->backend->cards['foo']['card2']);

    }

    function testDelete() {

        $this->ab->delete();
        $this->assertEquals(array(), $this->backend->addressBooks);

    }

    /**
     * @expectedException Sabre_DAV_Exception_MethodNotAllowed
     */
    function testSetName() {

        $this->ab->setName('foo');

    }

    function testGetLastModified() {

        $this->assertNull($this->ab->getLastModified());

    }

    function testUpdateProperties() {

        $this->assertTrue(
            $this->ab->updateProperties(array('{DAV:}displayname' => 'barrr'))
        );

        $this->assertEquals('barrr', $this->backend->addressBooks[0]['{DAV:}displayname']);

    }

    function testGetProperties() {

        $props = $this->ab->getProperties(array('{DAV:}displayname'));
        $this->assertEquals(array(
            '{DAV:}displayname' => 'd-name',
        ), $props);

    }

    function testACLMethods() {

        $this->assertEquals('principals/user1', $this->ab->getOwner());
        $this->assertNull($this->ab->getGroup());
        $this->assertEquals(array(
            array(
                'privilege' => '{DAV:}read',
                'principal' => 'principals/user1',
                'protected' => true,
            ),
            array(
                'privilege' => '{DAV:}write',
                'principal' => 'principals/user1',
                'protected' => true,
            ),
        ), $this->ab->getACL());

    }

    /**
     * @expectedException Sabre_DAV_Exception_MethodNotAllowed
     */
    function testSetACL() {

       $this->ab->setACL(array()); 

    }
        
}
