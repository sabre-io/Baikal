<?php

class Sabre_DAV_UUIDUtilTest extends PHPUnit_Framework_TestCase {

    function testValidateUUID() {

        $this->assertTrue(
            Sabre_DAV_UUIDUtil::validateUUID('11111111-2222-3333-4444-555555555555')
        );
        $this->assertFalse(
            Sabre_DAV_UUIDUtil::validateUUID(' 11111111-2222-3333-4444-555555555555')
        );
        $this->assertTrue(
            Sabre_DAV_UUIDUtil::validateUUID('ffffffff-2222-3333-4444-555555555555')
        );
        $this->assertFalse(
            Sabre_DAV_UUIDUtil::validateUUID('fffffffg-2222-3333-4444-555555555555')
        );


    }

}
