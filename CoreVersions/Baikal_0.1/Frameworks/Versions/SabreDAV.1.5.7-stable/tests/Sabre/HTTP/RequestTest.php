<?php

/**
 * @covers Sabre_HTTP_Request
 */
class Sabre_HTTP_RequestTest extends PHPUnit_Framework_TestCase {

    private $request;

    function setUp() {

        $server = array(
            'HTTP_HOST'      => 'www.example.org',
            'REQUEST_METHOD' => 'PUT',
            'REQUEST_URI'    => '/testuri/',
            'CONTENT_TYPE'   => 'text/xml',
        );

        $this->request = new Sabre_HTTP_Request($server);

    }

    function testGetHeader() {

        $this->assertEquals('www.example.org', $this->request->getHeader('Host'));
        $this->assertEquals('text/xml', $this->request->getHeader('Content-Type'));

    }

    function testGetNonExistantHeader() {

        $this->assertNull($this->request->getHeader('doesntexist'));
        $this->assertNull($this->request->getHeader('Content-Length'));

    }

    function testGetHeaders() {

        $expected = array(
            'host' => 'www.example.org',
            'content-type' => 'text/xml',
        );

        $this->assertEquals($expected, $this->request->getHeaders());

    }

    function testGetMethod() {

        $this->assertEquals('PUT', $this->request->getMethod(), 'It seems as if we didn\'t get a valid HTTP Request method back');

    }

    function testGetUri() {

        $this->assertEquals('/testuri/', $this->request->getUri(), 'We got an invalid uri back');

    }

    function testSetGetBody() {

        $h = fopen('php://memory','r+');
        fwrite($h,'testing');
        rewind($h);
        $this->request->setBody($h);
        $this->assertEquals('testing',$this->request->getBody(true),'We didn\'t get our testbody back');

    }

    function testSetGetBodyStream() {

        $h = fopen('php://memory','r+');
        fwrite($h,'testing');
        rewind($h);
        $this->request->setBody($h);
        $this->assertEquals('testing',stream_get_contents($this->request->getBody()),'We didn\'t get our testbody back');

    }


    function testDefaultInputStream() {

        $h = fopen('php://memory','r+');
        fwrite($h,'testing');
        rewind($h);

        $previousValue = Sabre_HTTP_Request::$defaultInputStream;
        Sabre_HTTP_Request::$defaultInputStream = $h;

        $this->assertEquals('testing',$this->request->getBody(true),'We didn\'t get our testbody back');
        Sabre_HTTP_Request::$defaultInputStream = $previousValue;

    }

    function testGetAbsoluteUri() {

        $s = array(
            'HTTP_HOST' => 'sabredav.org',
            'REQUEST_URI' => '/foo'
        );

        $r = new Sabre_HTTP_Request($s);

        $this->assertEquals('http://sabredav.org/foo', $r->getAbsoluteUri());

        $s = array(
            'HTTP_HOST'   => 'sabredav.org',
            'REQUEST_URI' => '/foo',
            'HTTPS'       => 'on',
        );

        $r = new Sabre_HTTP_Request($s);

        $this->assertEquals('https://sabredav.org/foo', $r->getAbsoluteUri());

    }

    function testGetQueryString() {

        $s = array(
            'QUERY_STRING' => 'bla',
        );

        $r = new Sabre_HTTP_Request($s);
        $this->assertEquals('bla', $r->getQueryString());

        $s = array();

        $r = new Sabre_HTTP_Request($s);
        $this->assertEquals('', $r->getQueryString());

    }




}

?>
