<?php

require_once 'Sabre/DAVACL/MockPrincipalBackend.php';

class Sabre_CalDAV_Principal_CollectionTest extends PHPUnit_Framework_TestCase {

    function testGetChildForPrincipal() {

        $back = new Sabre_DAVACL_MockPrincipalBackend();
        $col = new Sabre_CalDAV_Principal_Collection($back);
        $r = $col->getChildForPrincipal(array(
            'uri' => 'principals/admin',
        ));
        $this->assertInstanceOf('Sabre_CalDAV_Principal_User', $r);

    }

}
