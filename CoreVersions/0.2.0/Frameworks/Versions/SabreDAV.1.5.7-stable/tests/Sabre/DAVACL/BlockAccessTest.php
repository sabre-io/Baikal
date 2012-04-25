<?php

class Sabre_DAVACL_BlockAccessTest extends PHPUnit_Framework_TestCase {

    protected $server;
    protected $plugin;

    function setUp() {

        $nodes = array(
            new Sabre_DAV_SimpleDirectory('testdir'),
        );

        $this->server = new Sabre_DAV_Server($nodes);
        $this->plugin = new Sabre_DAVACL_Plugin();
        $this->plugin->allowAccessToNodesWithoutACL = false;
        $this->server->addPlugin($this->plugin);

    }

    /**
     * @expectedException Sabre_DAVACL_Exception_NeedPrivileges
     */
    function testGet() {

        $this->server->broadcastEvent('beforeMethod',array('GET','testdir'));

    }

    function testGetDoesntExist() {

        $r = $this->server->broadcastEvent('beforeMethod',array('GET','foo'));
        $this->assertTrue($r);

    }

    /**
     * @expectedException Sabre_DAVACL_Exception_NeedPrivileges
     */
    function testHEAD() {

        $this->server->broadcastEvent('beforeMethod',array('HEAD','testdir'));

    }

    /**
     * @expectedException Sabre_DAVACL_Exception_NeedPrivileges
     */
    function testOPTIONS() {

        $this->server->broadcastEvent('beforeMethod',array('OPTIONS','testdir'));

    }

    /**
     * @expectedException Sabre_DAVACL_Exception_NeedPrivileges
     */
    function testPUT() {

        $this->server->broadcastEvent('beforeMethod',array('PUT','testdir'));

    }

    /**
     * @expectedException Sabre_DAVACL_Exception_NeedPrivileges
     */
    function testPROPPATCH() {

        $this->server->broadcastEvent('beforeMethod',array('PROPPATCH','testdir'));

    }

    /**
     * @expectedException Sabre_DAVACL_Exception_NeedPrivileges
     */
    function testCOPY() {

        $this->server->broadcastEvent('beforeMethod',array('COPY','testdir'));

    }

    /**
     * @expectedException Sabre_DAVACL_Exception_NeedPrivileges
     */
    function testMOVE() {

        $this->server->broadcastEvent('beforeMethod',array('MOVE','testdir'));

    }

    /**
     * @expectedException Sabre_DAVACL_Exception_NeedPrivileges
     */
    function testACL() {

        $this->server->broadcastEvent('beforeMethod',array('ACL','testdir'));

    }

    /**
     * @expectedException Sabre_DAVACL_Exception_NeedPrivileges
     */
    function testLOCK() {

        $this->server->broadcastEvent('beforeMethod',array('LOCK','testdir'));

    }

    /**
     * @expectedException Sabre_DAVACL_Exception_NeedPrivileges
     */
    function testBeforeBind() {

        $this->server->broadcastEvent('beforeBind',array('testdir/file'));

    }

    /**
     * @expectedException Sabre_DAVACL_Exception_NeedPrivileges
     */
    function testBeforeUnbind() {

        $this->server->broadcastEvent('beforeUnbind',array('testdir'));

    }

    function testBeforeGetProperties() {

        $requestedProperties = array(
            '{DAV:}displayname', 
            '{DAV:}getcontentlength',
            '{DAV:}bar',
            '{DAV:}owner', 
        );
        $returnedProperties = array();

        $arguments = array(
            'testdir',
            new Sabre_DAV_SimpleDirectory('testdir'),
            &$requestedProperties,
            &$returnedProperties
        );
        $r = $this->server->broadcastEvent('beforeGetProperties',$arguments);
        $this->assertTrue($r);

        $expected = array(
            '403' => array(
                '{DAV:}displayname' => null,
                '{DAV:}getcontentlength' => null,
                '{DAV:}bar' => null,
                '{DAV:}owner' => null,
            ),
        );

        $this->assertEquals($expected, $returnedProperties);
        $this->assertEquals(array(), $requestedProperties);

    }

    function testBeforeGetPropertiesNoListing() {

        $this->plugin->hideNodesFromListings = true;

        $requestedProperties = array(
            '{DAV:}displayname', 
            '{DAV:}getcontentlength',
            '{DAV:}bar',
            '{DAV:}owner', 
        );
        $returnedProperties = array();

        $arguments = array(
            'testdir',
            new Sabre_DAV_SimpleDirectory('testdir'),
            &$requestedProperties,
            &$returnedProperties
        );
        $r = $this->server->broadcastEvent('beforeGetProperties',$arguments);
        $this->assertFalse($r);

    }
}
