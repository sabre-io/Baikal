<?php

class Sabre_CardDAV_AddressBookRootTest extends PHPUnit_Framework_TestCase {

    function testGetName() {

        $pBackend = new Sabre_DAVACL_MockPrincipalBackend();
        $cBackend = new Sabre_CardDAV_MockBackend();
        $root = new Sabre_CardDAV_AddressBookRoot($pBackend, $cBackend); 
        $this->assertEquals('addressbooks', $root->getName()); 

    }

    function testGetChildForPrincipal() {

        $pBackend = new Sabre_DAVACL_MockPrincipalBackend();
        $cBackend = new Sabre_CardDAV_MockBackend();
        $root = new Sabre_CardDAV_AddressBookRoot($pBackend, $cBackend);

        $children = $root->getChildren();
        $this->assertEquals(2, count($children));

        $this->assertInstanceOf('Sabre_CardDAV_UserAddressBooks', $children[0]);
        $this->assertEquals('user1', $children[0]->getName());

    }
}
