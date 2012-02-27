<?php

class Sabre_CardDAV_UserAddressBooksTest extends PHPUnit_Framework_TestCase {

    protected $s;
    protected $backend;

    function setUp() {

        $this->backend = new Sabre_CardDAV_MockBackend();
        $this->s = new Sabre_CardDAV_UserAddressBooks(
            $this->backend,
            'principals/user1'
        );

    }

    function testGetName() {

        $this->assertEquals('user1', $this->s->getName());

    }

    /**
     * @expectedException Sabre_DAV_Exception_MethodNotAllowed
     */
    function testSetName() {

        $this->s->setName('user2');

    }

    /**
     * @expectedException Sabre_DAV_Exception_MethodNotAllowed
     */
    function testDelete() {

        $this->s->delete();

    }

    function testGetLastModified() {

        $this->assertNull($this->s->getLastModified());

    }

    /**
     * @expectedException Sabre_DAV_Exception_MethodNotAllowed
     */
    function testCreateFile() {

        $this->s->createFile('bla');

    }

    /**
     * @expectedException Sabre_DAV_Exception_MethodNotAllowed
     */
    function testCreateDirectory() {

        $this->s->createDirectory('bla');

    }

    function testGetChild() {

        $child = $this->s->getChild('book1');
        $this->assertInstanceOf('Sabre_CardDAV_AddressBook', $child);
        $this->assertEquals('book1', $child->getName());

    }

    /**
     * @expectedException Sabre_DAV_Exception_FileNotFound
     */
    function testGetChild404() {

        $this->s->getChild('book2');

    }

    function testGetChildren() {

        $children = $this->s->getChildren();
        $this->assertEquals(1, count($children));
        $this->assertInstanceOf('Sabre_CardDAV_AddressBook', $children[0]);
        $this->assertEquals('book1', $children[0]->getName());

    }

    function testCreateExtendedCollection() {

        $resourceType = array(
            '{' . Sabre_CardDAV_Plugin::NS_CARDDAV . '}addressbook',
            '{DAV:}collection',
        );
        $this->s->createExtendedCollection('book2', $resourceType, array('{DAV:}displayname' => 'a-book 2'));

        $this->assertEquals(array(
            'id' => 'book2',
            'uri' => 'book2',
            '{DAV:}displayname' => 'a-book 2',
            'principaluri' => 'principals/user1',
        ), $this->backend->addressBooks[1]);

    }

    /**
     * @expectedException Sabre_DAV_Exception_InvalidResourceType
     */
    function testCreateExtendedCollectionInvalid() {

        $resourceType = array(
            '{DAV:}collection',
        );
        $this->s->createExtendedCollection('book2', $resourceType, array('{DAV:}displayname' => 'a-book 2'));

    }


    function testACLMethods() {

        $this->assertEquals('principals/user1', $this->s->getOwner());
        $this->assertNull($this->s->getGroup());
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
        ), $this->s->getACL());

    }

    /**
     * @expectedException Sabre_DAV_Exception_MethodNotAllowed
     */
    function testSetACL() {

       $this->s->setACL(array()); 

    }
}

?>
