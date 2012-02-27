<?php

class Sabre_CardDAV_IDirectoryTest extends PHPUnit_Framework_TestCase {

    function testResourceType() {

        $tree = array(
            new Sabre_CardDAV_DirectoryMock('directory')
        );

        $server = new Sabre_DAV_Server($tree);
        $plugin = new Sabre_CardDAV_Plugin();
        $server->addPlugin($plugin);

        $props = $server->getProperties('directory', array('{DAV:}resourcetype'));
        $this->assertTrue($props['{DAV:}resourcetype']->is('{' . Sabre_CardDAV_Plugin::NS_CARDDAV . '}directory'));

    }

}

class Sabre_CardDAV_DirectoryMock extends Sabre_DAV_SimpleDirectory implements Sabre_CardDAV_IDirectory {


}
