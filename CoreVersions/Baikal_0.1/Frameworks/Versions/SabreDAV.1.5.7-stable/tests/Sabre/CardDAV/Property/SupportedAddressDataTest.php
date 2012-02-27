<?php

class Sabre_CardDAV_Property_SupportedAddressDataDataTest extends PHPUnit_Framework_TestCase {

    function testSimple() {

        $property = new Sabre_CardDAV_Property_SupportedAddressData();

    }

    /**
     * @depends testSimple
     */
    function testSerialize() {

        $property = new Sabre_CardDAV_Property_SupportedAddressData();

        $doc = new DOMDocument();
        $root = $doc->createElementNS(Sabre_CardDAV_Plugin::NS_CARDDAV, 'card:root');
        $root->setAttribute('xmlns:d','DAV:');

        $doc->appendChild($root);
        $server = new Sabre_DAV_Server();

        $property->serialize($server, $root);

        $xml = $doc->saveXML();

        $this->assertEquals(
'<?xml version="1.0"?>
<card:root xmlns:card="' . Sabre_CardDAV_Plugin::NS_CARDDAV . '" xmlns:d="DAV:">' .
'<card:address-data-type content-type="text/vcard" version="3.0"/>' . 
'<card:address-data-type content-type="text/vcard" version="4.0"/>' . 
'</card:root>
', $xml);

    }

}
