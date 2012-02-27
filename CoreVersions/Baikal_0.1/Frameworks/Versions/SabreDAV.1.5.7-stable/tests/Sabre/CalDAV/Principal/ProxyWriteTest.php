<?php

class Sabre_CalDAV_Principal_ProxyWriteTest extends Sabre_CalDAV_Principal_ProxyReadTest {

    function getInstance() {

        $backend = new Sabre_DAVACL_MockPrincipalBackend();
        $principal = new Sabre_CalDAV_Principal_ProxyWrite($backend, array(
            'uri' => 'principal/user',
        ));
        $this->backend = $backend;
        return $principal;

    }

    function testGetName() {

        $i = $this->getInstance();
        $this->assertEquals('calendar-proxy-write', $i->getName());

    }
    function testGetDisplayName() {

        $i = $this->getInstance();
        $this->assertEquals('calendar-proxy-write', $i->getDisplayName());

    }

    function testGetPrincipalUri() {

        $i = $this->getInstance();
        $this->assertEquals('principal/user/calendar-proxy-write', $i->getPrincipalUrl());

    }

}
