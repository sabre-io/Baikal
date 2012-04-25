<?php

class Sabre_CalDAV_Principal_UserTest extends PHPUnit_Framework_TestCase {

    function getInstance() {

        $backend = new Sabre_DAVACL_MockPrincipalBackend();
        return new Sabre_CalDAV_Principal_User($backend, array(
            'uri' => 'principal/user',
        ));

    }

    /**
     * @expectedException Sabre_DAV_Exception_Forbidden
     */
    function testCreateFile() {

        $u = $this->getInstance();
        $u->createFile('test');

    }

    /**
     * @expectedException Sabre_DAV_Exception_Forbidden
     */
    function testCreateDirectory() {

        $u = $this->getInstance();
        $u->createDirectory('test');

    }

    function testGetChildProxyRead() {

        $u = $this->getInstance();
        $child = $u->getChild('calendar-proxy-read');
        $this->assertInstanceOf('Sabre_CalDAV_Principal_ProxyRead', $child);

    }

    function testGetChildProxyWrite() {

        $u = $this->getInstance();
        $child = $u->getChild('calendar-proxy-write');
        $this->assertInstanceOf('Sabre_CalDAV_Principal_ProxyWrite', $child);

    }

    /**
     * @expectedException Sabre_DAV_Exception_FileNotFound
     */
    function testGetChildNotFound() {

        $u = $this->getInstance();
        $child = $u->getChild('foo');

    }

    function testGetChildren() {

        $u = $this->getInstance();
        $children = $u->getChildren();
        $this->assertEquals(2, count($children));
        $this->assertInstanceOf('Sabre_CalDAV_Principal_ProxyRead', $children[0]);
        $this->assertInstanceOf('Sabre_CalDAV_Principal_ProxyWrite', $children[1]);

    }

    function testChildExist() {

        $u = $this->getInstance();
        $this->assertTrue($u->childExists('calendar-proxy-read'));
        $this->assertTrue($u->childExists('calendar-proxy-write'));
        $this->assertFalse($u->childExists('foo'));

    }

    function testGetACL() {

        $expected = array(
            array(
                'privilege' => '{DAV:}read',
                'principal' => 'principal/user',
                'protected' => true,
            ),
            array(
                'privilege' => '{DAV:}read',
                'principal' => 'principal/user/calendar-proxy-read',
                'protected' => true,
            ),
            array(
                'privilege' => '{DAV:}read',
                'principal' => 'principal/user/calendar-proxy-write',
                'protected' => true,
            ),
        );

        $u = $this->getInstance();
        $this->assertEquals($expected, $u->getACL());

    }

}
