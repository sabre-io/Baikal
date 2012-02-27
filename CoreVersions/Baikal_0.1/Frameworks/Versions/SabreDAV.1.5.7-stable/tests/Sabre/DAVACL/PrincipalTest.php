<?php

require_once 'Sabre/DAVACL/MockPrincipalBackend.php';

class Sabre_DAVACL_PrincipalTest extends PHPUnit_Framework_TestCase {

    public function testConstruct() {

        $principalBackend = new Sabre_DAVACL_MockPrincipalBackend();
        $principal = new Sabre_DAVACL_Principal($principalBackend, array('uri' => 'principals/admin'));
        $this->assertTrue($principal instanceof Sabre_DAVACL_Principal);

    }

    /**
     * @expectedException Sabre_DAV_Exception
     */
    public function testConstructNoUri() {

        $principalBackend = new Sabre_DAVACL_MockPrincipalBackend();
        $principal = new Sabre_DAVACL_Principal($principalBackend, array());

    }

    public function testGetName() {

        $principalBackend = new Sabre_DAVACL_MockPrincipalBackend();
        $principal = new Sabre_DAVACL_Principal($principalBackend, array('uri' => 'principals/admin'));
        $this->assertEquals('admin',$principal->getName());

    }

    public function testGetDisplayName() {

        $principalBackend = new Sabre_DAVACL_MockPrincipalBackend();
        $principal = new Sabre_DAVACL_Principal($principalBackend, array('uri' => 'principals/admin'));
        $this->assertEquals('admin',$principal->getDisplayname());

        $principal = new Sabre_DAVACL_Principal($principalBackend, array(
            'uri' => 'principals/admin',
            '{DAV:}displayname' => 'Mr. Admin'
        ));
        $this->assertEquals('Mr. Admin',$principal->getDisplayname());

    }

    public function testGetProperties() {

        $principalBackend = new Sabre_DAVACL_MockPrincipalBackend();
        $principal = new Sabre_DAVACL_Principal($principalBackend, array(
            'uri' => 'principals/admin',
            '{DAV:}displayname' => 'Mr. Admin',
            '{http://www.example.org/custom}custom' => 'Custom',
            '{http://sabredav.org/ns}email-address' => 'admin@example.org',
        ));

        $keys = array(
            '{DAV:}displayname',
            '{http://www.example.org/custom}custom',
            '{http://sabredav.org/ns}email-address',
        );
        $props = $principal->getProperties($keys);

        foreach($keys as $key) $this->assertArrayHasKey($key,$props);

        $this->assertEquals('Mr. Admin',$props['{DAV:}displayname']);

        $this->assertEquals('admin@example.org', $props['{http://sabredav.org/ns}email-address']);
    }

    public function testUpdateProperties() {
        
        $principalBackend = new Sabre_DAVACL_MockPrincipalBackend();
        $principal = new Sabre_DAVACL_Principal($principalBackend, array('uri' => 'principals/admin'));
        $result = $principal->updateProperties(array('{DAV:}yourmom'=>'test'));
        $this->assertEquals(false,$result);

    }

    public function testGetPrincipalUrl() {

        $principalBackend = new Sabre_DAVACL_MockPrincipalBackend();
        $principal = new Sabre_DAVACL_Principal($principalBackend, array('uri' => 'principals/admin'));
        $this->assertEquals('principals/admin',$principal->getPrincipalUrl());

    }

    public function testGetAlternateUriSet() {

        $principalBackend = new Sabre_DAVACL_MockPrincipalBackend();
        $principal = new Sabre_DAVACL_Principal($principalBackend, array(
            'uri' => 'principals/admin',
            '{DAV:}displayname' => 'Mr. Admin',
            '{http://www.example.org/custom}custom' => 'Custom',
            '{http://sabredav.org/ns}email-address' => 'admin@example.org',
            '{DAV:}alternate-URI-set' => array(
                'mailto:admin+1@example.org',
                'mailto:admin+2@example.org',
                'mailto:admin@example.org',
            ),
        ));

        $expected = array(
            'mailto:admin+1@example.org',
            'mailto:admin+2@example.org',
            'mailto:admin@example.org',
        );

        $this->assertEquals($expected,$principal->getAlternateUriSet());

    }
    public function testGetAlternateUriSetEmpty() {

        $principalBackend = new Sabre_DAVACL_MockPrincipalBackend();
        $principal = new Sabre_DAVACL_Principal($principalBackend, array(
            'uri' => 'principals/admin',
        ));

        $expected = array();

        $this->assertEquals($expected,$principal->getAlternateUriSet());

    }

    public function testGetGroupMemberSet() {

        $principalBackend = new Sabre_DAVACL_MockPrincipalBackend();
        $principal = new Sabre_DAVACL_Principal($principalBackend, array('uri' => 'principals/admin'));
        $this->assertEquals(array(),$principal->getGroupMemberSet());

    }
    public function testGetGroupMembership() {

        $principalBackend = new Sabre_DAVACL_MockPrincipalBackend();
        $principal = new Sabre_DAVACL_Principal($principalBackend, array('uri' => 'principals/admin'));
        $this->assertEquals(array(),$principal->getGroupMembership());

    }

    public function testSetGroupMemberSet() {

        $principalBackend = new Sabre_DAVACL_MockPrincipalBackend();
        $principal = new Sabre_DAVACL_Principal($principalBackend, array('uri' => 'principals/admin'));
        $principal->setGroupMemberSet(array('principals/foo'));

        $this->assertEquals(array(
            'principals/admin' => array('principals/foo'),
        ), $principalBackend->groupMembers);

    }

    public function testGetOwner() {

        $principalBackend = new Sabre_DAVACL_MockPrincipalBackend();
        $principal = new Sabre_DAVACL_Principal($principalBackend, array('uri' => 'principals/admin'));
        $this->assertEquals('principals/admin',$principal->getOwner());

    }

    public function testGetGroup() {

        $principalBackend = new Sabre_DAVACL_MockPrincipalBackend();
        $principal = new Sabre_DAVACL_Principal($principalBackend, array('uri' => 'principals/admin'));
        $this->assertNull($principal->getGroup());

    }

    public function testGetACl() {

        $principalBackend = new Sabre_DAVACL_MockPrincipalBackend();
        $principal = new Sabre_DAVACL_Principal($principalBackend, array('uri' => 'principals/admin'));
        $this->assertEquals(array(
            array(
                'privilege' => '{DAV:}read',
                'principal' => 'principals/admin',
                'protected' => true,
            )
        ),$principal->getACL());

    }

    /**
     * @expectedException Sabre_DAV_Exception_MethodNotAllowed
     */
    public function testSetACl() {

        $principalBackend = new Sabre_DAVACL_MockPrincipalBackend();
        $principal = new Sabre_DAVACL_Principal($principalBackend, array('uri' => 'principals/admin'));
        $principal->setACL(array());

    }
}
