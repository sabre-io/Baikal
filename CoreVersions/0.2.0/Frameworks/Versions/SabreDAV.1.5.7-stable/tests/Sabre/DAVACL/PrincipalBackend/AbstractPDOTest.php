<?php

abstract class Sabre_DAVACL_PrincipalBackend_AbstractPDOTest extends PHPUnit_Framework_TestCase {

    abstract function getPDO();

    function testConstruct() {

        $pdo = $this->getPDO();
        $backend = new Sabre_DAVACL_PrincipalBackend_PDO($pdo);
        $this->assertTrue($backend instanceof Sabre_DAVACL_PrincipalBackend_PDO);

    }

    /**
     * @depends testConstruct
     */
    function testGetPrincipalsByPrefix() {

        $pdo = $this->getPDO();
        $backend = new Sabre_DAVACL_PrincipalBackend_PDO($pdo);

        $expected = array(
            array(
                'uri' => 'principals/user',
                '{http://sabredav.org/ns}email-address' => 'user@example.org',
                '{DAV:}displayname' => 'User',
            ),
            array(
                'uri' => 'principals/group',
                '{http://sabredav.org/ns}email-address' => 'group@example.org',
                '{DAV:}displayname' => 'Group',
            ),
        );

        $this->assertEquals($expected, $backend->getPrincipalsByPrefix('principals'));
        $this->assertEquals(array(), $backend->getPrincipalsByPrefix('foo'));

    }

    /**
     * @depends testConstruct
     */
    function testGetPrincipalByPath() {

        $pdo = $this->getPDO();
        $backend = new Sabre_DAVACL_PrincipalBackend_PDO($pdo);

        $expected = array(
            'id' => 1,
            'uri' => 'principals/user',
            '{http://sabredav.org/ns}email-address' => 'user@example.org',
            '{DAV:}displayname' => 'User',
        );

        $this->assertEquals($expected, $backend->getPrincipalByPath('principals/user'));
        $this->assertEquals(null, $backend->getPrincipalByPath('foo'));

    }

    function testGetGroupMemberSet() {

        $pdo = $this->getPDO();
        $backend = new Sabre_DAVACL_PrincipalBackend_PDO($pdo);
        $expected = array('principals/user');

        $this->assertEquals($expected,$backend->getGroupMemberSet('principals/group'));

    }

    function testGetGroupMembership() {

        $pdo = $this->getPDO();
        $backend = new Sabre_DAVACL_PrincipalBackend_PDO($pdo);
        $expected = array('principals/group');

        $this->assertEquals($expected,$backend->getGroupMembership('principals/user'));

    }

    function testSetGroupMemberSet() {

        $pdo = $this->getPDO();

        // Start situation
        $backend = new Sabre_DAVACL_PrincipalBackend_PDO($pdo);
        $this->assertEquals(array('principals/user'), $backend->getGroupMemberSet('principals/group'));

        // Removing all principals
        $backend->setGroupMemberSet('principals/group', array());
        $this->assertEquals(array(), $backend->getGroupMemberSet('principals/group'));

        // Adding principals again
        $backend->setGroupMemberSet('principals/group', array('principals/user'));
        $this->assertEquals(array('principals/user'), $backend->getGroupMemberSet('principals/group'));


    }

}
