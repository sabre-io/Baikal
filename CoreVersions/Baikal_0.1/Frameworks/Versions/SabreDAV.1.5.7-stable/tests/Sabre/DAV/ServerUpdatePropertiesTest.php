<?php

class Sabre_DAV_ServerUpdatePropertiesTest extends PHPUnit_Framework_TestCase {

    function testUpdatePropertiesFail() {

        $tree = array(
            new Sabre_DAV_SimpleDirectory('foo'),
        );
        $server = new Sabre_DAV_Server($tree);
        
        $result = $server->updateProperties('foo', array(
            '{DAV:}foo' => 'bar'
        ));

        $expected = array(
            'href' => 'foo',
            '403' => array(
                '{DAV:}foo' => null,
            ),
        );
        $this->assertEquals($expected, $result);

    }

    function testUpdatePropertiesProtected() {

        $tree = array(
            new Sabre_DAV_SimpleDirectory('foo'),
        );
        $server = new Sabre_DAV_Server($tree);
        
        $result = $server->updateProperties('foo', array(
            '{DAV:}getetag' => 'bla',
            '{DAV:}foo' => 'bar'
        ));

        $expected = array(
            'href' => 'foo',
            '403' => array(
                '{DAV:}getetag' => null,
            ),
            '424' => array(
                '{DAV:}foo' => null,
            ),
        );
        $this->assertEquals($expected, $result);

    }

    function testUpdatePropertiesEventFail() {

        $tree = array(
            new Sabre_DAV_SimpleDirectory('foo'),
        );
        $server = new Sabre_DAV_Server($tree);
        $server->subscribeEvent('updateProperties', array($this,'updatepropfail'));
        
        $result = $server->updateProperties('foo', array(
            '{DAV:}foo' => 'bar',
            '{DAV:}foo2' => 'bla',
        ));

        $expected = array(
            'href' => 'foo',
            '404' => array(
                '{DAV:}foo' => null,
            ),
            '424' => array(
                '{DAV:}foo2' => null,
            ),
        );
        $this->assertEquals($expected, $result);

    }

    function updatePropFail(&$propertyDelta, &$result, $node) {

        $result[404] = array(
            '{DAV:}foo' => null,
        );
        unset($propertyDelta['{DAV:}foo']);
        return false;

    }


    function testUpdatePropertiesEventSuccess() {

        $tree = array(
            new Sabre_DAV_SimpleDirectory('foo'),
        );
        $server = new Sabre_DAV_Server($tree);
        $server->subscribeEvent('updateProperties', array($this,'updatepropsuccess'));
        
        $result = $server->updateProperties('foo', array(
            '{DAV:}foo' => 'bar',
            '{DAV:}foo2' => 'bla',
        ));

        $expected = array(
            'href' => 'foo',
            '200' => array(
                '{DAV:}foo' => null,
            ),
            '201' => array(
                '{DAV:}foo2' => null,
            ),
        );
        $this->assertEquals($expected, $result);

    }

    function updatePropSuccess(&$propertyDelta, &$result, $node) {

        $result[200] = array(
            '{DAV:}foo' => null,
        );
        $result[201] = array(
            '{DAV:}foo2' => null,
        );
        unset($propertyDelta['{DAV:}foo']);
        unset($propertyDelta['{DAV:}foo2']);
        return; 

    }
}
