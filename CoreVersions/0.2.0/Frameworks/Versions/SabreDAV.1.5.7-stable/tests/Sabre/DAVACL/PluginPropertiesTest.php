<?php

require_once 'Sabre/DAV/Auth/MockBackend.php';
require_once 'Sabre/DAVACL/MockPrincipal.php';

class Sabre_DAVACL_PluginPropertiesTest extends PHPUnit_Framework_TestCase {

    function testPrincipalCollectionSet() {

        $plugin = new Sabre_DAVACL_Plugin();
        $plugin->principalCollectionSet = array(
            'principals1',
            'principals2',
        );

        $requestedProperties = array(
            '{DAV:}principal-collection-set',
        );

        $returnedProperties = array(
            200 => array(),
            404 => array(),
        );

        $server = new Sabre_DAV_Server();
        $server->addPlugin($plugin);

        $this->assertNull($plugin->beforeGetProperties('', new Sabre_DAV_SimpleDirectory('root'), $requestedProperties, $returnedProperties));

        $this->assertEquals(1,count($returnedProperties[200]));
        $this->assertArrayHasKey('{DAV:}principal-collection-set',$returnedProperties[200]);
        $this->assertInstanceOf('Sabre_DAV_Property_HrefList', $returnedProperties[200]['{DAV:}principal-collection-set']);

        $expected = array(
            'principals1/',
            'principals2/',
        );
        

        $this->assertEquals($expected, $returnedProperties[200]['{DAV:}principal-collection-set']->getHrefs());


    }

    function testCurrentUserPrincipal() {

        $fakeServer = new Sabre_DAV_Server();
        $plugin = new Sabre_DAV_Auth_Plugin(new Sabre_DAV_Auth_MockBackend(),'realm');
        $fakeServer->addPlugin($plugin);
        $plugin = new Sabre_DAVACL_Plugin();
        $fakeServer->addPlugin($plugin);

        
        $requestedProperties = array(
            '{DAV:}current-user-principal',
        );

        $returnedProperties = array(
            200 => array(),
            404 => array(),
        );

        $this->assertNull($plugin->beforeGetProperties('', new Sabre_DAV_SimpleDirectory('root'), $requestedProperties, $returnedProperties));

        $this->assertEquals(1,count($returnedProperties[200]));
        $this->assertArrayHasKey('{DAV:}current-user-principal',$returnedProperties[200]);
        $this->assertInstanceOf('Sabre_DAVACL_Property_Principal', $returnedProperties[200]['{DAV:}current-user-principal']);
        $this->assertEquals(Sabre_DAVACL_Property_Principal::UNAUTHENTICATED, $returnedProperties[200]['{DAV:}current-user-principal']->getType());

        // This will force the login
        $fakeServer->broadCastEvent('beforeMethod',array('GET',''));


        $requestedProperties = array(
            '{DAV:}current-user-principal',
        );

        $returnedProperties = array(
            200 => array(),
            404 => array(),
        );


        $this->assertNull($plugin->beforeGetProperties('', new Sabre_DAV_SimpleDirectory('root'), $requestedProperties, $returnedProperties));


        $this->assertEquals(1,count($returnedProperties[200]));
        $this->assertArrayHasKey('{DAV:}current-user-principal',$returnedProperties[200]);
        $this->assertInstanceOf('Sabre_DAVACL_Property_Principal', $returnedProperties[200]['{DAV:}current-user-principal']);
        $this->assertEquals(Sabre_DAVACL_Property_Principal::HREF, $returnedProperties[200]['{DAV:}current-user-principal']->getType());
        $this->assertEquals('principals/admin/', $returnedProperties[200]['{DAV:}current-user-principal']->getHref());

    }

    function testSupportedPrivilegeSet() {

        $plugin = new Sabre_DAVACL_Plugin();
        $server = new Sabre_DAV_Server();
        $server->addPlugin($plugin);

        $requestedProperties = array(
            '{DAV:}supported-privilege-set',
        );

        $returnedProperties = array(
            200 => array(),
            404 => array(),
        );


        $this->assertNull($plugin->beforeGetProperties('', new Sabre_DAV_SimpleDirectory('root'), $requestedProperties, $returnedProperties));

        $this->assertEquals(1,count($returnedProperties[200]));
        $this->assertArrayHasKey('{DAV:}supported-privilege-set',$returnedProperties[200]);
        $this->assertInstanceOf('Sabre_DAVACL_Property_SupportedPrivilegeSet', $returnedProperties[200]['{DAV:}supported-privilege-set']);

        $server = new Sabre_DAV_Server();
        $prop = $returnedProperties[200]['{DAV:}supported-privilege-set'];

        $dom = new DOMDocument('1.0', 'utf-8');
        $root = $dom->createElement('d:root');
        $root->setAttribute('xmlns:d','DAV:');
        $dom->appendChild($root);
        $prop->serialize($server, $root);


        $xpaths = array(
            '/d:root' => 1,
            '/d:root/d:supported-privilege' => 1,
            '/d:root/d:supported-privilege/d:privilege' => 1,
            '/d:root/d:supported-privilege/d:privilege/d:all' => 1,
            '/d:root/d:supported-privilege/d:abstract' => 1,
            '/d:root/d:supported-privilege/d:supported-privilege' => 2,
            '/d:root/d:supported-privilege/d:supported-privilege/d:privilege' => 2,
            '/d:root/d:supported-privilege/d:supported-privilege/d:privilege/d:read' => 1,
            '/d:root/d:supported-privilege/d:supported-privilege/d:privilege/d:write' => 1,
            '/d:root/d:supported-privilege/d:supported-privilege/d:supported-privilege' => 8,
            '/d:root/d:supported-privilege/d:supported-privilege/d:supported-privilege/d:privilege' => 8,
            '/d:root/d:supported-privilege/d:supported-privilege/d:supported-privilege/d:privilege/d:read-acl' => 1,
            '/d:root/d:supported-privilege/d:supported-privilege/d:supported-privilege/d:privilege/d:read-current-user-privilege-set' => 1,
            '/d:root/d:supported-privilege/d:supported-privilege/d:supported-privilege/d:privilege/d:write-content' => 1,
            '/d:root/d:supported-privilege/d:supported-privilege/d:supported-privilege/d:privilege/d:write-properties' => 1,
            '/d:root/d:supported-privilege/d:supported-privilege/d:supported-privilege/d:privilege/d:write-acl' => 1,
            '/d:root/d:supported-privilege/d:supported-privilege/d:supported-privilege/d:privilege/d:bind' => 1,
            '/d:root/d:supported-privilege/d:supported-privilege/d:supported-privilege/d:privilege/d:unbind' => 1,
            '/d:root/d:supported-privilege/d:supported-privilege/d:supported-privilege/d:privilege/d:unlock' => 1,
            '/d:root/d:supported-privilege/d:supported-privilege/d:supported-privilege/d:abstract' => 8,
        );


        // reloading because php dom sucks 
        $dom2 = new DOMDocument('1.0', 'utf-8');
        $dom2->loadXML($dom->saveXML());

        $dxpath = new DOMXPath($dom2);
        $dxpath->registerNamespace('d','DAV:');
        foreach($xpaths as $xpath=>$count) {

            $this->assertEquals($count, $dxpath->query($xpath)->length, 'Looking for : ' . $xpath . ', we could only find ' . $dxpath->query($xpath)->length . ' elements, while we expected ' . $count);

        }

    }

    function testACL() {

        $plugin = new Sabre_DAVACL_Plugin();

        $nodes = array(
            new Sabre_DAVACL_MockACLNode('foo', array(
                array(
                    'principal' => 'principals/admin',
                    'privilege' => '{DAV:}read',
                )
            )),
            new Sabre_DAV_SimpleDirectory('principals', array(
                $principal = new Sabre_DAVACL_MockPrincipal('admin','principals/admin'),
            )),
            
        );

        $server = new Sabre_DAV_Server($nodes);
        $server->addPlugin($plugin);
        $authPlugin = new Sabre_DAV_Auth_Plugin(new Sabre_DAV_Auth_MockBackend(),'realm');
        $server->addPlugin($authPlugin);

        // Force login
        $authPlugin->beforeMethod('BLA','foo');

        $requestedProperties = array(
            '{DAV:}acl',
        );

        $returnedProperties = array(
            200 => array(),
            404 => array(),
        );


        $this->assertNull($plugin->beforeGetProperties('foo', $nodes[0], $requestedProperties, $returnedProperties));

        $this->assertEquals(1,count($returnedProperties[200]),'The {DAV:}acl property did not return from the list. Full list: ' . print_r($returnedProperties,true));
        $this->assertArrayHasKey('{DAV:}acl',$returnedProperties[200]);
        $this->assertInstanceOf('Sabre_DAVACL_Property_ACL', $returnedProperties[200]['{DAV:}acl']);

    }

    function testAlternateUriSet() {

        $tree = array(
            new Sabre_DAV_SimpleDirectory('principals', array(
                $principal = new Sabre_DAVACL_MockPrincipal('user','principals/user'),
            )),
        );

        $fakeServer = new Sabre_DAV_Server($tree);
        //$plugin = new Sabre_DAV_Auth_Plugin(new Sabre_DAV_Auth_MockBackend(),'realm');
        //$fakeServer->addPlugin($plugin);
        $plugin = new Sabre_DAVACL_Plugin();
        $fakeServer->addPlugin($plugin);

        $requestedProperties = array(
            '{DAV:}alternate-URI-set',
        );
        $returnedProperties = array();

        $result = $plugin->beforeGetProperties('principals/user',$principal,$requestedProperties,$returnedProperties);

        $this->assertNull($result);

        $this->assertTrue(isset($returnedProperties[200]));
        $this->assertTrue(isset($returnedProperties[200]['{DAV:}alternate-URI-set']));
        $this->assertInstanceOf('Sabre_DAV_Property_HrefList', $returnedProperties[200]['{DAV:}alternate-URI-set']);

        $this->assertEquals(array(), $returnedProperties[200]['{DAV:}alternate-URI-set']->getHrefs());

    }

    function testPrincipalURL() {

        $tree = array(
            new Sabre_DAV_SimpleDirectory('principals', array(
                $principal = new Sabre_DAVACL_MockPrincipal('user','principals/user'),
            )),
        );

        $fakeServer = new Sabre_DAV_Server($tree);
        //$plugin = new Sabre_DAV_Auth_Plugin(new Sabre_DAV_Auth_MockBackend(),'realm');
        //$fakeServer->addPlugin($plugin);
        $plugin = new Sabre_DAVACL_Plugin();
        $fakeServer->addPlugin($plugin);

        $requestedProperties = array(
            '{DAV:}principal-URL',
        );
        $returnedProperties = array();

        $result = $plugin->beforeGetProperties('principals/user',$principal,$requestedProperties,$returnedProperties);

        $this->assertNull($result);

        $this->assertTrue(isset($returnedProperties[200]));
        $this->assertTrue(isset($returnedProperties[200]['{DAV:}principal-URL']));
        $this->assertInstanceOf('Sabre_DAV_Property_Href', $returnedProperties[200]['{DAV:}principal-URL']);

        $this->assertEquals('principals/user/', $returnedProperties[200]['{DAV:}principal-URL']->getHref());

    }

    function testGroupMemberSet() {

        $tree = array(
            new Sabre_DAV_SimpleDirectory('principals', array(
                $principal = new Sabre_DAVACL_MockPrincipal('user','principals/user'),
            )),
        );

        $fakeServer = new Sabre_DAV_Server($tree);
        //$plugin = new Sabre_DAV_Auth_Plugin(new Sabre_DAV_Auth_MockBackend(),'realm');
        //$fakeServer->addPlugin($plugin);
        $plugin = new Sabre_DAVACL_Plugin();
        $fakeServer->addPlugin($plugin);

        $requestedProperties = array(
            '{DAV:}group-member-set',
        );
        $returnedProperties = array();

        $result = $plugin->beforeGetProperties('principals/user',$principal,$requestedProperties,$returnedProperties);

        $this->assertNull($result);

        $this->assertTrue(isset($returnedProperties[200]));
        $this->assertTrue(isset($returnedProperties[200]['{DAV:}group-member-set']));
        $this->assertInstanceOf('Sabre_DAV_Property_HrefList', $returnedProperties[200]['{DAV:}group-member-set']);

        $this->assertEquals(array(), $returnedProperties[200]['{DAV:}group-member-set']->getHrefs());

    }

    function testGroupMemberShip() {

        $tree = array(
            new Sabre_DAV_SimpleDirectory('principals', array(
                $principal = new Sabre_DAVACL_MockPrincipal('user','principals/user'),
            )),
        );

        $fakeServer = new Sabre_DAV_Server($tree);
        //$plugin = new Sabre_DAV_Auth_Plugin(new Sabre_DAV_Auth_MockBackend(),'realm');
        //$fakeServer->addPlugin($plugin);
        $plugin = new Sabre_DAVACL_Plugin();
        $fakeServer->addPlugin($plugin);

        $requestedProperties = array(
            '{DAV:}group-membership',
        );
        $returnedProperties = array();

        $result = $plugin->beforeGetProperties('principals/user',$principal,$requestedProperties,$returnedProperties);

        $this->assertNull($result);

        $this->assertTrue(isset($returnedProperties[200]));
        $this->assertTrue(isset($returnedProperties[200]['{DAV:}group-membership']));
        $this->assertInstanceOf('Sabre_DAV_Property_HrefList', $returnedProperties[200]['{DAV:}group-membership']);

        $this->assertEquals(array(), $returnedProperties[200]['{DAV:}group-membership']->getHrefs());

    }

    function testGetDisplayName() {

        $tree = array(
            new Sabre_DAV_SimpleDirectory('principals', array(
                $principal = new Sabre_DAVACL_MockPrincipal('user','principals/user'),
            )),
        );

        $fakeServer = new Sabre_DAV_Server($tree);
        //$plugin = new Sabre_DAV_Auth_Plugin(new Sabre_DAV_Auth_MockBackend(),'realm');
        //$fakeServer->addPlugin($plugin);
        $plugin = new Sabre_DAVACL_Plugin();
        $fakeServer->addPlugin($plugin);

        $requestedProperties = array(
            '{DAV:}displayname',
        );
        $returnedProperties = array();

        $result = $plugin->beforeGetProperties('principals/user',$principal,$requestedProperties,$returnedProperties);

        $this->assertNull($result);

        $this->assertTrue(isset($returnedProperties[200]));
        $this->assertTrue(isset($returnedProperties[200]['{DAV:}displayname']));

        $this->assertEquals('user', $returnedProperties[200]['{DAV:}displayname']);

    }
}
