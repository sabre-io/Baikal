<?php

require_once 'Sabre/HTTP/ResponseMock.php';
require_once 'Sabre/DAV/Auth/MockBackend.php';
require_once 'Sabre/DAVACL/MockPrincipalBackend.php';

class Sabre_DAVACL_PrincipalPropertySearchTest extends PHPUnit_Framework_TestCase {
    
    function getServer() {

        $backend = new Sabre_DAVACL_MockPrincipalBackend();

        $dir = new Sabre_DAV_SimpleDirectory('root');
        $principals = new Sabre_DAVACL_PrincipalCollection($backend);
        $dir->addChild($principals);

        $fakeServer = new Sabre_DAV_Server(new Sabre_DAV_ObjectTree($dir));
        $fakeServer->httpResponse = new Sabre_HTTP_ResponseMock();
        $fakeServer->debugExceptions = true;
        $plugin = new Sabre_DAVACL_MockPlugin($backend,'realm');
        $plugin->allowAccessToNodesWithoutACL = true;

        $this->assertTrue($plugin instanceof Sabre_DAVACL_Plugin);
        $fakeServer->addPlugin($plugin);
        $this->assertEquals($plugin, $fakeServer->getPlugin('acl'));

        return $fakeServer;

    }
    
    function testDepth1() {

        $xml = '<?xml version="1.0"?>
<d:principal-property-search xmlns:d="DAV:">
  <d:property-search>
     <d:prop>
       <d:displayname />
     </d:prop>
     <d:match>user</d:match>
  </d:property-search>
  <d:prop>
    <d:displayname />
    <d:getcontentlength />
  </d:prop>
</d:principal-property-search>';

        $serverVars = array(
            'REQUEST_METHOD' => 'REPORT',
            'HTTP_DEPTH'     => '1',
            'REQUEST_URI'    => '/principals',
        );

        $request = new Sabre_HTTP_Request($serverVars);
        $request->setBody($xml);

        $server = $this->getServer();
        $server->httpRequest = $request;

        $server->exec();

        $this->assertEquals('HTTP/1.1 400 Bad request', $server->httpResponse->status);
        $this->assertEquals(array(
            'Content-Type' => 'application/xml; charset=utf-8',
        ), $server->httpResponse->headers);

    }

    
    function testUnknownSearchField() {

        $xml = '<?xml version="1.0"?>
<d:principal-property-search xmlns:d="DAV:">
  <d:property-search>
     <d:prop>
       <d:yourmom />
     </d:prop>
     <d:match>user</d:match>
  </d:property-search>
  <d:prop>
    <d:displayname />
    <d:getcontentlength />
  </d:prop>
</d:principal-property-search>';

        $serverVars = array(
            'REQUEST_METHOD' => 'REPORT',
            'HTTP_DEPTH'     => '0',
            'REQUEST_URI'    => '/principals',
        );

        $request = new Sabre_HTTP_Request($serverVars);
        $request->setBody($xml);

        $server = $this->getServer();
        $server->httpRequest = $request;

        $server->exec();

        $this->assertEquals('HTTP/1.1 207 Multi-Status', $server->httpResponse->status);
        $this->assertEquals(array(
            'Content-Type' => 'application/xml; charset=utf-8',
        ), $server->httpResponse->headers);

    }

    function testCorrect() {

        $xml = '<?xml version="1.0"?>
<d:principal-property-search xmlns:d="DAV:">
  <d:property-search>
     <d:prop>
       <d:displayname />
     </d:prop>
     <d:match>user</d:match>
  </d:property-search>
  <d:prop>
    <d:displayname />
    <d:getcontentlength />
  </d:prop>
</d:principal-property-search>';

        $serverVars = array(
            'REQUEST_METHOD' => 'REPORT',
            'HTTP_DEPTH'     => '0',
            'REQUEST_URI'    => '/principals',
        );

        $request = new Sabre_HTTP_Request($serverVars);
        $request->setBody($xml);

        $server = $this->getServer();
        $server->httpRequest = $request;

        $server->exec();

        $this->assertEquals('HTTP/1.1 207 Multi-Status', $server->httpResponse->status, $server->httpResponse->body);
        $this->assertEquals(array(
            'Content-Type' => 'application/xml; charset=utf-8',
        ), $server->httpResponse->headers);

        
        $check = array(
            '/d:multistatus',
            '/d:multistatus/d:response' => 1,
            '/d:multistatus/d:response/d:href' => 1,
            '/d:multistatus/d:response/d:propstat' => 2,
            '/d:multistatus/d:response/d:propstat/d:prop' => 2,
            '/d:multistatus/d:response/d:propstat/d:prop/d:displayname' => 1,
            '/d:multistatus/d:response/d:propstat/d:prop/d:getcontentlength' => 1,
            '/d:multistatus/d:response/d:propstat/d:status' => 2,
        );

        $xml = simplexml_load_string($server->httpResponse->body);
        $xml->registerXPathNamespace('d','DAV:');
        foreach($check as $v1=>$v2) {

            $xpath = is_int($v1)?$v2:$v1;

            $result = $xml->xpath($xpath);

            $count = 1;
            if (!is_int($v1)) $count = $v2;

            $this->assertEquals($count,count($result), 'we expected ' . $count . ' appearances of ' . $xpath . ' . We found ' . count($result) . '. Full response body: ' . $server->httpResponse->body);

        }

    }
}

class Sabre_DAVACL_MockPlugin extends Sabre_DAVACL_Plugin {

    function getCurrentUserPrivilegeSet($node) {

        return array(
            '{DAV:}read',
            '{DAV:}write',
        );

    }

}
