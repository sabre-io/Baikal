<?php

require_once 'Sabre/DAVACL/MockPrincipal.php';

class Sabre_DAVACL_PluginUpdatePropertiesTest extends PHPUnit_Framework_TestCase {

    public function testUpdatePropertiesPassthrough() {

        $tree = array(
            new Sabre_DAV_SimpleDirectory('foo'),
        );
        $server = new Sabre_DAV_Server($tree);
        $server->addPlugin(new Sabre_DAVACL_Plugin());

        $result = $server->updateProperties('foo', array(
            '{DAV:}foo' => 'bar',
        ));

        $expected = array(
            'href' => 'foo',
            '403' => array(
                '{DAV:}foo' => null,
            ),
        );

        $this->assertEquals($expected, $result);

    }

    public function testRemoveGroupMembers() {

        $tree = array(
            new Sabre_DAVACL_MockPrincipal('foo','foo'),
        );
        $server = new Sabre_DAV_Server($tree);
        $server->addPlugin(new Sabre_DAVACL_Plugin());

        $result = $server->updateProperties('foo', array(
            '{DAV:}group-member-set' => null,
        ));

        $expected = array(
            'href' => 'foo',
            '200' => array(
                '{DAV:}group-member-set' => null,
            ),
        );

        $this->assertEquals($expected, $result);
        $this->assertEquals(array(),$tree[0]->getGroupMemberSet());

    }

    public function testSetGroupMembers() {

        $tree = array(
            new Sabre_DAVACL_MockPrincipal('foo','foo'),
        );
        $server = new Sabre_DAV_Server($tree);
        $server->addPlugin(new Sabre_DAVACL_Plugin());

        $result = $server->updateProperties('foo', array(
            '{DAV:}group-member-set' => new Sabre_DAV_Property_HrefList(array('bar','baz')),
        ));

        $expected = array(
            'href' => 'foo',
            '200' => array(
                '{DAV:}group-member-set' => null,
            ),
        );

        $this->assertEquals($expected, $result);
        $this->assertEquals(array('bar','baz'),$tree[0]->getGroupMemberSet());

    }

    /**
     * @expectedException sabre_DAV_Exception
     */
    public function testSetBadValue() {

        $tree = array(
            new Sabre_DAVACL_MockPrincipal('foo','foo'),
        );
        $server = new Sabre_DAV_Server($tree);
        $server->addPlugin(new Sabre_DAVACL_Plugin());

        $result = $server->updateProperties('foo', array(
            '{DAV:}group-member-set' => new StdClass(),
        ));

    }

    public function testSetBadNode() {

        $tree = array(
            new Sabre_DAV_SimpleDirectory('foo'),
        );
        $server = new Sabre_DAV_Server($tree);
        $server->addPlugin(new Sabre_DAVACL_Plugin());

        $result = $server->updateProperties('foo', array(
            '{DAV:}group-member-set' => new Sabre_DAV_Property_HrefList(array('bar','baz')),
            '{DAV:}bar' => 'baz',
        ));

        $expected = array(
            'href' => 'foo',
            '403' => array(
                '{DAV:}group-member-set' => null,
            ),
            '424' => array(
                '{DAV:}bar' => null,
            ),
        );

        $this->assertEquals($expected, $result);

    }
}

?>
