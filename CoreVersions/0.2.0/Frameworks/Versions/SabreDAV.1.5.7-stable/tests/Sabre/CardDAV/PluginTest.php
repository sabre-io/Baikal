<?php

require_once 'Sabre/DAVACL/MockPrincipalBackend.php';

class Sabre_CardDAV_PluginTest extends Sabre_CardDAV_AbstractPluginTest {

    function testConstruct() {

        $this->assertEquals('card', $this->server->xmlNamespaces[Sabre_CardDAV_Plugin::NS_CARDDAV]);
        $this->assertEquals('{' . Sabre_CardDAV_Plugin::NS_CARDDAV . '}addressbook', $this->server->resourceTypeMapping['Sabre_CardDAV_IAddressBook']);
        
        $this->assertTrue(in_array('addressbook', $this->plugin->getFeatures()));

    }

    function testSupportedReportSet() {

        $this->assertEquals(array(
            '{' . Sabre_CardDAV_Plugin::NS_CARDDAV . '}addressbook-multiget',
            '{' . Sabre_CardDAV_Plugin::NS_CARDDAV . '}addressbook-query',
        ), $this->plugin->getSupportedReportSet('addressbooks/user1/book1'));

    }

    function testSupportedReportSetEmpty() {

        $this->assertEquals(array(
        ), $this->plugin->getSupportedReportSet(''));

    }

    function testAddressBookHomeSet() {

        $result = $this->server->getProperties('principals/user1', array('{' . Sabre_CardDAV_Plugin::NS_CARDDAV . '}addressbook-home-set'));

        $this->assertEquals(1, count($result));
        $this->assertTrue(isset($result['{' . Sabre_CardDAV_Plugin::NS_CARDDAV . '}addressbook-home-set']));
        $this->assertEquals('addressbooks/user1/', $result['{' . Sabre_CardDAV_Plugin::NS_CARDDAV . '}addressbook-home-set']->getHref());

    }

    function testDirectoryGateway() {

        $result = $this->server->getProperties('principals/user1', array('{' . Sabre_CardDAV_Plugin::NS_CARDDAV . '}directory-gateway'));

        $this->assertEquals(1, count($result));
        $this->assertTrue(isset($result['{' . Sabre_CardDAV_Plugin::NS_CARDDAV . '}directory-gateway']));
        $this->assertEquals(array('directory'), $result['{' . Sabre_CardDAV_Plugin::NS_CARDDAV . '}directory-gateway']->getHrefs());

    }

    function testReportPassThrough() {

        $this->assertNull($this->plugin->report('{DAV:}foo', new DomDocument()));

    }


}
