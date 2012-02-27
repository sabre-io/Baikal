<?php

class Sabre_VObject_Issue153Test extends PHPUnit_Framework_TestCase {

    function testRead() {

        $obj = Sabre_VObject_Reader::read(file_get_contents(dirname(__FILE__) . '/issue153.vcf'));
        $this->assertEquals('Test Benutzer', (string)$obj->fn);

    }

}
