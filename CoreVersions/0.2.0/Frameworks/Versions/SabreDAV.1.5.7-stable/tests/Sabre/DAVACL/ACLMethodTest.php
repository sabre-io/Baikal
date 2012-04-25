<?php

class Sabre_DAVACL_ACLMethodTest extends PHPUnit_Framework_TestCase {

    /**
     * @expectedException Sabre_DAV_Exception_BadRequest
     */
    function testCallback() {

        $acl = new Sabre_DAVACL_Plugin();
        $server = new Sabre_DAV_Server();
        $server->addPlugin($acl);

        $acl->unknownMethod('ACL','test'); 

    }

    function testCallbackPassthru() {

        $acl = new Sabre_DAVACL_Plugin();
        $server = new Sabre_DAV_Server();
        $server->addPlugin($acl);

        $this->assertNull($acl->unknownMethod('FOO','test')); 

    }

    /**

    /**
     * @expectedException Sabre_DAV_Exception_MethodNotAllowed 
     */
    function testNotSupportedByNode() {

        $tree = array(
            new Sabre_DAV_SimpleDirectory('test'),
        );
        $acl = new Sabre_DAVACL_Plugin();
        $server = new Sabre_DAV_Server($tree);
        $server->httpRequest = new Sabre_HTTP_Request();
        $body = '<?xml version="1.0"?>
<d:acl xmlns:d="DAV:">
</d:acl>';
        $server->httpRequest->setBody($body);
        $server->addPlugin($acl);

        $acl->httpACL('test'); 

    }

    function testSuccessSimple() {

        $tree = array(
            new Sabre_DAVACL_MockACLNode('test',array()),
        );
        $acl = new Sabre_DAVACL_Plugin();
        $server = new Sabre_DAV_Server($tree);
        $server->httpRequest = new Sabre_HTTP_Request();
        $body = '<?xml version="1.0"?>
<d:acl xmlns:d="DAV:">
</d:acl>';
        $server->httpRequest->setBody($body);
        $server->addPlugin($acl);

        $this->assertNull($acl->httpACL('test')); 

    }

    /**
     * @expectedException Sabre_DAVACL_Exception_NotRecognizedPrincipal
     */
    function testUnrecognizedPrincipal() {

        $tree = array(
            new Sabre_DAVACL_MockACLNode('test',array()),
        );
        $acl = new Sabre_DAVACL_Plugin();
        $server = new Sabre_DAV_Server($tree);
        $server->httpRequest = new Sabre_HTTP_Request();
        $body = '<?xml version="1.0"?>
<d:acl xmlns:d="DAV:">
    <d:ace>
        <d:grant><d:privilege><d:read /></d:privilege></d:grant>
        <d:principal><d:href>/principals/notfound</d:href></d:principal>
    </d:ace>
</d:acl>';
        $server->httpRequest->setBody($body);
        $server->addPlugin($acl);

        $acl->httpACL('test'); 

    }

    /**
     * @expectedException Sabre_DAVACL_Exception_NotRecognizedPrincipal
     */
    function testUnrecognizedPrincipal2() {

        $tree = array(
            new Sabre_DAVACL_MockACLNode('test',array()),
            new Sabre_DAV_SimpleDirectory('principals',array(
                new Sabre_DAV_SimpleDirectory('notaprincipal'),
            )),
        );
        $acl = new Sabre_DAVACL_Plugin();
        $server = new Sabre_DAV_Server($tree);
        $server->httpRequest = new Sabre_HTTP_Request();
        $body = '<?xml version="1.0"?>
<d:acl xmlns:d="DAV:">
    <d:ace>
        <d:grant><d:privilege><d:read /></d:privilege></d:grant>
        <d:principal><d:href>/principals/notaprincipal</d:href></d:principal>
    </d:ace>
</d:acl>';
        $server->httpRequest->setBody($body);
        $server->addPlugin($acl);

        $acl->httpACL('test'); 

    }

    /**
     * @expectedException Sabre_DAVACL_Exception_NotSupportedPrivilege
     */
    function testUnknownPrivilege() {

        $tree = array(
            new Sabre_DAVACL_MockACLNode('test',array()),
        );
        $acl = new Sabre_DAVACL_Plugin();
        $server = new Sabre_DAV_Server($tree);
        $server->httpRequest = new Sabre_HTTP_Request();
        $body = '<?xml version="1.0"?>
<d:acl xmlns:d="DAV:">
    <d:ace>
        <d:grant><d:privilege><d:bananas /></d:privilege></d:grant>
        <d:principal><d:href>/principals/notfound</d:href></d:principal>
    </d:ace>
</d:acl>';
        $server->httpRequest->setBody($body);
        $server->addPlugin($acl);

        $acl->httpACL('test'); 

    }

    /**
     * @expectedException Sabre_DAVACL_Exception_NoAbstract
     */
    function testAbstractPrivilege() {

        $tree = array(
            new Sabre_DAVACL_MockACLNode('test',array()),
        );
        $acl = new Sabre_DAVACL_Plugin();
        $server = new Sabre_DAV_Server($tree);
        $server->httpRequest = new Sabre_HTTP_Request();
        $body = '<?xml version="1.0"?>
<d:acl xmlns:d="DAV:">
    <d:ace>
        <d:grant><d:privilege><d:read-acl /></d:privilege></d:grant>
        <d:principal><d:href>/principals/notfound</d:href></d:principal>
    </d:ace>
</d:acl>';
        $server->httpRequest->setBody($body);
        $server->addPlugin($acl);

        $acl->httpACL('test'); 

    }

    /**
     * @expectedException Sabre_DAVACL_Exception_AceConflict
     */
    function testUpdateProtectedPrivilege() {

        $oldACL = array(
            array(
                'principal' => 'principals/notfound',
                'privilege' => '{DAV:}write',
                'protected' => true,
            ),
        );

        $tree = array(
            new Sabre_DAVACL_MockACLNode('test',$oldACL),
        );
        $acl = new Sabre_DAVACL_Plugin();
        $server = new Sabre_DAV_Server($tree);
        $server->httpRequest = new Sabre_HTTP_Request();
        $body = '<?xml version="1.0"?>
<d:acl xmlns:d="DAV:">
    <d:ace>
        <d:grant><d:privilege><d:read /></d:privilege></d:grant>
        <d:principal><d:href>/principals/notfound</d:href></d:principal>
    </d:ace>
</d:acl>';
        $server->httpRequest->setBody($body);
        $server->addPlugin($acl);

        $acl->httpACL('test'); 

    }

    /**
     * @expectedException Sabre_DAVACL_Exception_AceConflict
     */
    function testUpdateProtectedPrivilege2() {

        $oldACL = array(
            array(
                'principal' => 'principals/notfound',
                'privilege' => '{DAV:}write',
                'protected' => true,
            ),
        );

        $tree = array(
            new Sabre_DAVACL_MockACLNode('test',$oldACL),
        );
        $acl = new Sabre_DAVACL_Plugin();
        $server = new Sabre_DAV_Server($tree);
        $server->httpRequest = new Sabre_HTTP_Request();
        $body = '<?xml version="1.0"?>
<d:acl xmlns:d="DAV:">
    <d:ace>
        <d:grant><d:privilege><d:write /></d:privilege></d:grant>
        <d:principal><d:href>/principals/foo</d:href></d:principal>
    </d:ace>
</d:acl>';
        $server->httpRequest->setBody($body);
        $server->addPlugin($acl);

        $acl->httpACL('test'); 

    }

    /**
     * @expectedException Sabre_DAVACL_Exception_AceConflict
     */
    function testUpdateProtectedPrivilege3() {

        $oldACL = array(
            array(
                'principal' => 'principals/notfound',
                'privilege' => '{DAV:}write',
                'protected' => true,
            ),
        );

        $tree = array(
            new Sabre_DAVACL_MockACLNode('test',$oldACL),
        );
        $acl = new Sabre_DAVACL_Plugin();
        $server = new Sabre_DAV_Server($tree);
        $server->httpRequest = new Sabre_HTTP_Request();
        $body = '<?xml version="1.0"?>
<d:acl xmlns:d="DAV:">
    <d:ace>
        <d:grant><d:privilege><d:write /></d:privilege></d:grant>
        <d:principal><d:href>/principals/notfound</d:href></d:principal>
    </d:ace>
</d:acl>';
        $server->httpRequest->setBody($body);
        $server->addPlugin($acl);

        $acl->httpACL('test'); 

    }

    function testSuccessComplex () {

        $oldACL = array(
            array(
                'principal' => 'principals/foo',
                'privilege' => '{DAV:}write',
                'protected' => true,
            ),
            array(
                'principal' => 'principals/bar',
                'privilege' => '{DAV:}read',
            ),
        );

        $tree = array(
            $node = new Sabre_DAVACL_MockACLNode('test',$oldACL),
            new Sabre_DAV_SimpleDirectory('principals', array(
                new Sabre_DAVACL_MockPrincipal('foo','principals/foo'),
                new Sabre_DAVACL_MockPrincipal('baz','principals/baz'),
            )),
        );
        $acl = new Sabre_DAVACL_Plugin();
        $server = new Sabre_DAV_Server($tree);
        $server->httpRequest = new Sabre_HTTP_Request();
        $body = '<?xml version="1.0"?>
<d:acl xmlns:d="DAV:">
    <d:ace>
        <d:grant><d:privilege><d:write /></d:privilege></d:grant>
        <d:principal><d:href>/principals/foo</d:href></d:principal>
        <d:protected />
    </d:ace>
    <d:ace>
        <d:grant><d:privilege><d:write /></d:privilege></d:grant>
        <d:principal><d:href>/principals/baz</d:href></d:principal>
    </d:ace>
</d:acl>';
        $server->httpRequest->setBody($body);
        $server->addPlugin($acl);

        $this->assertFalse($acl->unknownMethod('ACL','test')); 

        $this->assertEquals(array(
            array(
                'principal' => 'principals/foo',
                'privilege' => '{DAV:}write',
                'protected' => true,
            ),
            array(
                'principal' => 'principals/baz',
                'privilege' => '{DAV:}write',
                'protected' => false,
            ),
        ), $node->getACL());

    }
}
