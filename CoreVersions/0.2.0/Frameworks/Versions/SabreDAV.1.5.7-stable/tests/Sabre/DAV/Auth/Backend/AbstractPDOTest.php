<?php

abstract class Sabre_DAV_Auth_Backend_AbstractPDOTest extends PHPUnit_Framework_TestCase {

    abstract function getPDO();

    function testConstruct() {

        $pdo = $this->getPDO();
        $backend = new Sabre_DAV_Auth_Backend_PDO($pdo);
        $this->assertTrue($backend instanceof Sabre_DAV_Auth_Backend_PDO);

    }

    /**
     * @depends testConstruct
     */
    function testUserInfo() {

        $pdo = $this->getPDO();
        $backend = new Sabre_DAV_Auth_Backend_PDO($pdo);

        $this->assertNull($backend->getDigestHash('realm','blabla'));

        $expected = 'hash'; 

        $this->assertEquals($expected, $backend->getDigestHash('realm','user'));

    }

}
