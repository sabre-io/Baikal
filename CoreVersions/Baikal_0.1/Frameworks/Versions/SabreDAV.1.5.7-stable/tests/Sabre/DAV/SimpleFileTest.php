<?php

class Sabre_DAV_SimpleFileTest extends PHPUnit_Framework_TestCAse {

    function testAll() {

        $file = new Sabre_DAV_SimpleFile('filename.txt','contents','text/plain');

        $this->assertEquals('filename.txt', $file->getName());
        $this->assertEquals('contents', $file->get());
        $this->assertEquals('8', $file->getSize());
        $this->assertEquals('"' . md5('contents') . '"', $file->getETag());
        $this->assertEquals('text/plain', $file->getContentType());

    }

}

?>
