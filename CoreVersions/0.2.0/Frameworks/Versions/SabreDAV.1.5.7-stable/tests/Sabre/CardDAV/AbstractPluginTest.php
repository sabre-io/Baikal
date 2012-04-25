<?php

require_once 'Sabre/CardDAV/MockBackend.php';
require_once 'Sabre/DAVACL/MockPrincipalBackend.php';

abstract class Sabre_CardDAV_AbstractPluginTest extends PHPUnit_Framework_TestCase {

    protected $plugin;
    protected $server;
    protected $backend;

    function setUp() {

        $this->backend = new Sabre_CardDAV_MockBackend();
        $principalBackend = new Sabre_DAVACL_MockPrincipalBackend();

        $tree = array(
            new Sabre_CardDAV_AddressBookRoot($principalBackend, $this->backend),
            new Sabre_DAVACL_PrincipalCollection($principalBackend)
        );

        $this->plugin = new Sabre_CardDAV_Plugin();
        $this->plugin->directories = array('directory');
        $this->server = new Sabre_DAV_Server($tree);
        $this->server->addPlugin($this->plugin);
        $this->server->debugExceptions = true;

    }

}
