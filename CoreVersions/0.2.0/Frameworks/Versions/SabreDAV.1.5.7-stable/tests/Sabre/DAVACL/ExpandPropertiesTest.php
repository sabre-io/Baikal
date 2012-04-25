<?php

require_once 'Sabre/HTTP/ResponseMock.php';

class Sabre_DAVACL_ExpandPropertiesTest extends PHPUnit_Framework_TestCase {

    function getServer() {

        $tree = array(
            new Sabre_DAVACL_MockPropertyNode('node1', array(
                '{http://sabredav.org/ns}simple' => 'foo',
                '{http://sabredav.org/ns}href'   => new Sabre_DAV_Property_Href('node2'),
                '{DAV:}displayname'     => 'Node 1',
            )),
            new Sabre_DAVACL_MockPropertyNode('node2', array(
                '{http://sabredav.org/ns}simple' => 'simple',
                '{http://sabredav.org/ns}hreflist' => new Sabre_DAV_Property_HrefList(array('node1','node3')),
                '{DAV:}displayname'     => 'Node 2',
            )),
            new Sabre_DAVACL_MockPropertyNode('node3', array(
                '{http://sabredav.org/ns}simple' => 'simple',
                '{DAV:}displayname'     => 'Node 3',
            )),
        );

        $fakeServer = new Sabre_DAV_Server($tree);
        $fakeServer->debugExceptions = true;
        $fakeServer->httpResponse = new Sabre_HTTP_ResponseMock();
        $plugin = new Sabre_DAVACL_Plugin();
        $plugin->allowAccessToNodesWithoutACL = true;

        $this->assertTrue($plugin instanceof Sabre_DAVACL_Plugin);
        $fakeServer->addPlugin($plugin);
        $this->assertEquals($plugin, $fakeServer->getPlugin('acl'));

        return $fakeServer;

    }

    function testSimple() {

        $xml = '<?xml version="1.0"?>
<d:expand-property xmlns:d="DAV:">
  <d:property name="displayname" />
  <d:property name="foo" namespace="http://www.sabredav.org/NS/2010/nonexistant" />
  <d:property name="simple" namespace="http://sabredav.org/ns" />
  <d:property name="href" namespace="http://sabredav.org/ns" />
</d:expand-property>';

        $serverVars = array(
            'REQUEST_METHOD' => 'REPORT',
            'HTTP_DEPTH'     => '0',
            'REQUEST_URI'    => '/node1',
        );

        $request = new Sabre_HTTP_Request($serverVars);
        $request->setBody($xml);

        $server = $this->getServer();
        $server->httpRequest = $request;

        $server->exec();

        $this->assertEquals('HTTP/1.1 207 Multi-Status', $server->httpResponse->status,'Incorrect status code received. Full body: ' . $server->httpResponse->body);
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
            '/d:multistatus/d:response/d:propstat/d:prop/s:simple' => 1,
            '/d:multistatus/d:response/d:propstat/d:prop/s:href' => 1,
            '/d:multistatus/d:response/d:propstat/d:prop/s:href/d:href' => 1,
        );

        $xml = simplexml_load_string($server->httpResponse->body);
        $xml->registerXPathNamespace('d','DAV:');
        $xml->registerXPathNamespace('s','http://sabredav.org/ns');
        foreach($check as $v1=>$v2) {

            $xpath = is_int($v1)?$v2:$v1;

            $result = $xml->xpath($xpath);

            $count = 1;
            if (!is_int($v1)) $count = $v2;

            $this->assertEquals($count,count($result), 'we expected ' . $count . ' appearances of ' . $xpath . ' . We found ' . count($result) . '. Full response: ' . $server->httpResponse->body);

        }

    }

    /**
     * @depends testSimple
     */
    function testExpand() {

        $xml = '<?xml version="1.0"?>
<d:expand-property xmlns:d="DAV:">
  <d:property name="href" namespace="http://sabredav.org/ns">
      <d:property name="displayname" />
  </d:property>
</d:expand-property>';

        $serverVars = array(
            'REQUEST_METHOD' => 'REPORT',
            'HTTP_DEPTH'     => '0',
            'REQUEST_URI'    => '/node1',
        );

        $request = new Sabre_HTTP_Request($serverVars);
        $request->setBody($xml);

        $server = $this->getServer();
        $server->httpRequest = $request;

        $server->exec();

        $this->assertEquals('HTTP/1.1 207 Multi-Status', $server->httpResponse->status, 'Incorrect response status received. Full response body: ' . $server->httpResponse->body);
        $this->assertEquals(array(
            'Content-Type' => 'application/xml; charset=utf-8',
        ), $server->httpResponse->headers);

  
        $check = array(
            '/d:multistatus',
            '/d:multistatus/d:response' => 1,
            '/d:multistatus/d:response/d:href' => 1,
            '/d:multistatus/d:response/d:propstat' => 1,
            '/d:multistatus/d:response/d:propstat/d:prop' => 1,
            '/d:multistatus/d:response/d:propstat/d:prop/s:href' => 1,
            '/d:multistatus/d:response/d:propstat/d:prop/s:href/d:response' => 1,
            '/d:multistatus/d:response/d:propstat/d:prop/s:href/d:response/d:href' => 1,
            '/d:multistatus/d:response/d:propstat/d:prop/s:href/d:response/d:propstat' => 1,
            '/d:multistatus/d:response/d:propstat/d:prop/s:href/d:response/d:propstat/d:prop' => 1,
            '/d:multistatus/d:response/d:propstat/d:prop/s:href/d:response/d:propstat/d:prop/d:displayname' => 1,
        );

        $xml = simplexml_load_string($server->httpResponse->body);
        $xml->registerXPathNamespace('d','DAV:');
        $xml->registerXPathNamespace('s','http://sabredav.org/ns');
        foreach($check as $v1=>$v2) {

            $xpath = is_int($v1)?$v2:$v1;

            $result = $xml->xpath($xpath);

            $count = 1;
            if (!is_int($v1)) $count = $v2;

            $this->assertEquals($count,count($result), 'we expected ' . $count . ' appearances of ' . $xpath . ' . We found ' . count($result));

        }

    }

    /**
     * @depends testSimple
     */
    function testExpandHrefList() {

        $xml = '<?xml version="1.0"?>
<d:expand-property xmlns:d="DAV:">
  <d:property name="hreflist" namespace="http://sabredav.org/ns">
      <d:property name="displayname" />
  </d:property>
</d:expand-property>';

        $serverVars = array(
            'REQUEST_METHOD' => 'REPORT',
            'HTTP_DEPTH'     => '0',
            'REQUEST_URI'    => '/node2',
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

  
        $check = array(
            '/d:multistatus',
            '/d:multistatus/d:response' => 1,
            '/d:multistatus/d:response/d:href' => 1,
            '/d:multistatus/d:response/d:propstat' => 1,
            '/d:multistatus/d:response/d:propstat/d:prop' => 1,
            '/d:multistatus/d:response/d:propstat/d:prop/s:hreflist' => 1,
            '/d:multistatus/d:response/d:propstat/d:prop/s:hreflist/d:response' => 2,
            '/d:multistatus/d:response/d:propstat/d:prop/s:hreflist/d:response/d:href' => 2,
            '/d:multistatus/d:response/d:propstat/d:prop/s:hreflist/d:response/d:propstat' => 2,
            '/d:multistatus/d:response/d:propstat/d:prop/s:hreflist/d:response/d:propstat/d:prop' => 2,
            '/d:multistatus/d:response/d:propstat/d:prop/s:hreflist/d:response/d:propstat/d:prop/d:displayname' => 2,
        );

        $xml = simplexml_load_string($server->httpResponse->body);
        $xml->registerXPathNamespace('d','DAV:');
        $xml->registerXPathNamespace('s','http://sabredav.org/ns');
        foreach($check as $v1=>$v2) {

            $xpath = is_int($v1)?$v2:$v1;

            $result = $xml->xpath($xpath);

            $count = 1;
            if (!is_int($v1)) $count = $v2;

            $this->assertEquals($count,count($result), 'we expected ' . $count . ' appearances of ' . $xpath . ' . We found ' . count($result));

        }

    }

    /**
     * @depends testExpand
     */
    function testExpandDeep() {

        $xml = '<?xml version="1.0"?>
<d:expand-property xmlns:d="DAV:">
  <d:property name="hreflist" namespace="http://sabredav.org/ns">
      <d:property name="href" namespace="http://sabredav.org/ns">
          <d:property name="displayname" />
      </d:property>
      <d:property name="displayname" />
  </d:property>
</d:expand-property>';

        $serverVars = array(
            'REQUEST_METHOD' => 'REPORT',
            'HTTP_DEPTH'     => '0',
            'REQUEST_URI'    => '/node2',
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

  
        $check = array(
            '/d:multistatus',
            '/d:multistatus/d:response' => 1,
            '/d:multistatus/d:response/d:href' => 1,
            '/d:multistatus/d:response/d:propstat' => 1,
            '/d:multistatus/d:response/d:propstat/d:prop' => 1,
            '/d:multistatus/d:response/d:propstat/d:prop/s:hreflist' => 1,
            '/d:multistatus/d:response/d:propstat/d:prop/s:hreflist/d:response' => 2,
            '/d:multistatus/d:response/d:propstat/d:prop/s:hreflist/d:response/d:href' => 2,
            '/d:multistatus/d:response/d:propstat/d:prop/s:hreflist/d:response/d:propstat' => 3,
            '/d:multistatus/d:response/d:propstat/d:prop/s:hreflist/d:response/d:propstat/d:prop' => 3,
            '/d:multistatus/d:response/d:propstat/d:prop/s:hreflist/d:response/d:propstat/d:prop/d:displayname' => 2,
            '/d:multistatus/d:response/d:propstat/d:prop/s:hreflist/d:response/d:propstat/d:prop/s:href' => 2,
            '/d:multistatus/d:response/d:propstat/d:prop/s:hreflist/d:response/d:propstat/d:prop/s:href/d:response' => 1,
            '/d:multistatus/d:response/d:propstat/d:prop/s:hreflist/d:response/d:propstat/d:prop/s:href/d:response/d:href' => 1,
            '/d:multistatus/d:response/d:propstat/d:prop/s:hreflist/d:response/d:propstat/d:prop/s:href/d:response/d:propstat' => 1,
            '/d:multistatus/d:response/d:propstat/d:prop/s:hreflist/d:response/d:propstat/d:prop/s:href/d:response/d:propstat/d:prop' => 1,
            '/d:multistatus/d:response/d:propstat/d:prop/s:hreflist/d:response/d:propstat/d:prop/s:href/d:response/d:propstat/d:prop/d:displayname' => 1,
        );

        $xml = simplexml_load_string($server->httpResponse->body);
        $xml->registerXPathNamespace('d','DAV:');
        $xml->registerXPathNamespace('s','http://sabredav.org/ns');
        foreach($check as $v1=>$v2) {

            $xpath = is_int($v1)?$v2:$v1;

            $result = $xml->xpath($xpath);

            $count = 1;
            if (!is_int($v1)) $count = $v2;

            $this->assertEquals($count,count($result), 'we expected ' . $count . ' appearances of ' . $xpath . ' . We found ' . count($result));

        }

    }
}
class Sabre_DAVACL_MockPropertyNode implements Sabre_DAV_INode, Sabre_DAV_IProperties {

    function __construct($name, array $properties) {

        $this->name = $name;
        $this->properties = $properties;

    }

    function getName() {

        return $this->name;

    }

    function getProperties($requestedProperties) {

        $returnedProperties = array();
        foreach($requestedProperties as $requestedProperty) {
            if (isset($this->properties[$requestedProperty])) {
                $returnedProperties[$requestedProperty] = 
                    $this->properties[$requestedProperty];
            }
        }
        return $returnedProperties;

    }

    function delete() {

        throw new Sabre_DAV_Exception('Not implemented');

    }

    function setName($name) {

        throw new Sabre_DAV_Exception('Not implemented');

    }

    function getLastModified() {

        return null;

    }

    function updateProperties($properties) {

        throw new Sabre_DAV_Exception('Not implemented');

    }

}
