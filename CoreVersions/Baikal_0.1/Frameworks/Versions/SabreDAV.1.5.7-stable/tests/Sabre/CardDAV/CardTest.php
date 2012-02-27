<?php

class Sabre_CardDAV_CardTest extends PHPUnit_Framework_TestCase {

    protected $card;
    protected $backend;

    function setUp() {

        $this->backend = new Sabre_CardDAV_MockBackend();
        $this->card = new Sabre_CardDAV_Card(
            $this->backend,
            array(
                'uri' => 'book1',
                'id' => 'foo',
                'principaluri' => 'principals/user1',
            ),
            array(
                'uri' => 'card1',
                'addressbookid' => 'foo',
                'carddata' => 'card',
            )
        );

    }

    function testGet() {

        $result = $this->card->get();
        $this->assertEquals('card', $result);

    }


    /**
     * @depends testGet
     */
    function testPut() {

        $file = fopen('php://memory','r+');
        fwrite($file, 'newdata');
        rewind($file);
        $this->card->put($file);
        $result = $this->card->get();
        $this->assertEquals('newdata', $result);

    }


    function testDelete() {

        $this->card->delete();
        $this->assertEquals(1, count($this->backend->cards['foo']));

    }

    function testGetContentType() {

        $this->assertEquals('text/x-vcard', $this->card->getContentType());

    }

    function testGetETag() {

        $this->assertEquals('"' . md5('card') . '"' , $this->card->getETag());

    }

    function testGetETag2() {

        $card = new Sabre_CardDAV_Card(
            $this->backend,
            array(
                'uri' => 'book1',
                'id' => 'foo',
                'principaluri' => 'principals/user1',
            ),
            array(
                'uri' => 'card1',
                'addressbookid' => 'foo',
                'carddata' => 'card',
                'etag' => '"blabla"',
            )
        );
        $this->assertEquals('"blabla"' , $card->getETag());

    }

    function testGetLastModified() {

        $this->assertEquals(null, $this->card->getLastModified());

    }

    function testGetSize() {

        $this->assertEquals(4, $this->card->getSize());

    }

    function testACLMethods() {

        $this->assertEquals('principals/user1', $this->card->getOwner());
        $this->assertNull($this->card->getGroup());
        $this->assertEquals(array(
            array(
                'privilege' => '{DAV:}read',
                'principal' => 'principals/user1',
                'protected' => true,
            ),
            array(
                'privilege' => '{DAV:}write',
                'principal' => 'principals/user1',
                'protected' => true,
            ),
        ), $this->card->getACL());

    }

    /**
     * @expectedException Sabre_DAV_Exception_MethodNotAllowed
     */
    function testSetACL() {

       $this->card->setACL(array()); 

    }
}
