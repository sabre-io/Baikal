<?php

class Sabre_CardDAV_VersionTest extends PHPUnit_Framework_TestCase {

    function testString() {

        $v = Sabre_CardDAV_Version::VERSION;
        $this->assertEquals(-1, version_compare('0.1',$v));

        $s = Sabre_CardDAV_Version::STABILITY;
        $this->assertTrue($s == 'alpha' || $s == 'beta' || $s =='stable');

    }

}
