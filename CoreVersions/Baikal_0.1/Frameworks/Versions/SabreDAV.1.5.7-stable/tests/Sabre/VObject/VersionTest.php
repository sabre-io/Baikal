<?php

class Sabre_VObject_VersionTest extends PHPUnit_Framework_TestCase {

    function testString() {

        $v = Sabre_VObject_Version::VERSION;
        $this->assertEquals(-1, version_compare('0.9.0',$v));

        $s = Sabre_VObject_Version::STABILITY;
        $this->assertTrue($s == 'alpha' || $s == 'beta' || $s =='stable');

    }

}
