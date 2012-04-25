<?php

require_once 'Sabre/CalDAV/TestUtil.php';
require_once 'Sabre/DAV/Auth/MockBackend.php';
require_once 'Sabre/HTTP/ResponseMock.php';

class Sabre_CalDAV_ICSExportPluginTest extends PHPUnit_Framework_TestCase {

    function testInit() {

        $p = new Sabre_CalDAV_ICSExportPlugin();
        $s = new Sabre_DAV_Server();
        $s->addPlugin($p);

    }

    function testBeforeMethod() {

        if (!SABRE_HASSQLITE) $this->markTestSkipped('SQLite driver is not available');
        $cbackend = Sabre_CalDAV_TestUtil::getBackend();
        $pbackend = new Sabre_DAVACL_MockPrincipalBackend();

        $props = array(
            'uri'=>'UUID-123467', 
            'principaluri' => 'admin',
            'id' => 1,
        );
        $tree = array(
            new Sabre_CalDAV_Calendar($pbackend,$cbackend,$props),
        );

        $p = new Sabre_CalDAV_ICSExportPlugin();

        $s = new Sabre_DAV_Server($tree);
        $s->addPlugin($p);
        $s->addPlugin(new Sabre_CalDAV_Plugin());

        $h = new Sabre_HTTP_Request(array(
            'QUERY_STRING' => 'export',
        ));

        $s->httpRequest = $h;
        $s->httpResponse = new Sabre_HTTP_ResponseMock();

        $this->assertFalse($p->beforeMethod('GET','UUID-123467?export'));

        $this->assertEquals('HTTP/1.1 200 OK',$s->httpResponse->status);
        $this->assertEquals(array(
            'Content-Type' => 'text/calendar',
        ), $s->httpResponse->headers);

        $obj = Sabre_VObject_Reader::read($s->httpResponse->body);

        $this->assertEquals(5,count($obj->children()));
        $this->assertEquals(1,count($obj->VERSION));
        $this->assertEquals(1,count($obj->CALSCALE));
        $this->assertEquals(1,count($obj->PRODID));
        $this->assertEquals(1,count($obj->VTIMEZONE));
        $this->assertEquals(1,count($obj->VEVENT));

    }

    function testBeforeMethodNoGET() {

        $p = new Sabre_CalDAV_ICSExportPlugin();

        $s = new Sabre_DAV_Server();
        $s->addPlugin($p);

        $this->assertNull($p->beforeMethod('POST','UUID-123467?export'));

    }

    function testBeforeMethodNoExport() {

        $p = new Sabre_CalDAV_ICSExportPlugin();

        $s = new Sabre_DAV_Server();
        $s->addPlugin($p);

        $this->assertNull($p->beforeMethod('GET','UUID-123467'));

    }

    /**
     * @expectedException Sabre_DAVACL_Exception_NeedPrivileges
     */
    function testACLIntegration() {

        if (!SABRE_HASSQLITE) $this->markTestSkipped('SQLite driver is not available');
        $cbackend = Sabre_CalDAV_TestUtil::getBackend();
        $pbackend = new Sabre_DAVACL_MockPrincipalBackend();

        $props = array(
            'uri'=>'UUID-123467', 
            'principaluri' => 'admin',
            'id' => 1,
        );
        $tree = array(
            new Sabre_CalDAV_Calendar($pbackend,$cbackend,$props),
        );

        $p = new Sabre_CalDAV_ICSExportPlugin();

        $s = new Sabre_DAV_Server($tree);
        $s->addPlugin($p);
        $s->addPlugin(new Sabre_CalDAV_Plugin());
        $s->addPlugin(new Sabre_DAVACL_Plugin());

        $h = new Sabre_HTTP_Request(array(
            'QUERY_STRING' => 'export',
        ));

        $s->httpRequest = $h;
        $s->httpResponse = new Sabre_HTTP_ResponseMock();

        $p->beforeMethod('GET','UUID-123467?export');

    }

}
