<?php

require_once 'Sabre/DAV/ClientMock.php';

class Sabre_DAV_ClientTest extends PHPUnit_Framework_TestCase {

    function testConstruct() {

        $client = new Sabre_DAV_ClientMock(array(
            'baseUri' => '/',
        ));

    }

    /**
     * @expectedException InvalidArgumentException
     */
    function testConstructNoBaseUri() {

        $client = new Sabre_DAV_ClientMock(array());

    }

    function testRequest() {

        $client = new Sabre_DAV_ClientMock(array(
            'baseUri' => 'http://example.org/foo/bar/',
        ));

        $responseBlob = array(
            "HTTP/1.1 200 OK",
            "Content-Type: text/plain",
            "",
            "Hello there!"
        );

        $client->response = array(
            implode("\r\n", $responseBlob),
            array(
                'header_size' => 45,
                'http_code' => 200,
            ),
            0,
            "" 
        );

        $result = $client->request('POST', 'baz', 'sillybody', array('Content-Type' => 'text/plain'));
        
        $this->assertEquals('http://example.org/foo/bar/baz', $client->url);
        $this->assertEquals(array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'sillybody',
            CURLOPT_HEADER => true,
            CURLOPT_HTTPHEADER => array('Content-Type: text/plain'),
        ), $client->curlSettings);

        $this->assertEquals(array(
            'statusCode' => 200,
            'headers' => array(
                'content-type' => 'text/plain',
            ),
            'body' => 'Hello there!'
        ), $result);


    }


    function testRequestProxy() {

        $client = new Sabre_DAV_ClientMock(array(
            'baseUri' => 'http://example.org/foo/bar/',
            'proxy' => 'http://localhost:8000/',
        ));

        $responseBlob = array(
            "HTTP/1.1 200 OK",
            "Content-Type: text/plain",
            "",
            "Hello there!"
        );

        $client->response = array(
            implode("\r\n", $responseBlob),
            array(
                'header_size' => 45,
                'http_code' => 200,
            ),
            0,
            "" 
        );

        $result = $client->request('POST', 'baz', 'sillybody', array('Content-Type' => 'text/plain'));
        
        $this->assertEquals('http://example.org/foo/bar/baz', $client->url);
        $this->assertEquals(array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'sillybody',
            CURLOPT_HEADER => true,
            CURLOPT_HTTPHEADER => array('Content-Type: text/plain'),
            CURLOPT_PROXY => 'http://localhost:8000/',
        ), $client->curlSettings);

        $this->assertEquals(array(
            'statusCode' => 200,
            'headers' => array(
                'content-type' => 'text/plain',
            ),
            'body' => 'Hello there!'
        ), $result);

    }


    function testRequestAuth() {

        $client = new Sabre_DAV_ClientMock(array(
            'baseUri' => 'http://example.org/foo/bar/',
            'userName' => 'user',
            'password' => 'password',
        ));

        $responseBlob = array(
            "HTTP/1.1 200 OK",
            "Content-Type: text/plain",
            "",
            "Hello there!"
        );

        $client->response = array(
            implode("\r\n", $responseBlob),
            array(
                'header_size' => 45,
                'http_code' => 200,
            ),
            0,
            "" 
        );

        $result = $client->request('POST', 'baz', 'sillybody', array('Content-Type' => 'text/plain'));
        
        $this->assertEquals('http://example.org/foo/bar/baz', $client->url);
        $this->assertEquals(array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'sillybody',
            CURLOPT_HEADER => true,
            CURLOPT_HTTPHEADER => array('Content-Type: text/plain'),
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC | CURLAUTH_DIGEST,
            CURLOPT_USERPWD => 'user:password'
        ), $client->curlSettings);

        $this->assertEquals(array(
            'statusCode' => 200,
            'headers' => array(
                'content-type' => 'text/plain',
            ),
            'body' => 'Hello there!'
        ), $result);

    }

    function testRequestError() {

        $client = new Sabre_DAV_ClientMock(array(
            'baseUri' => 'http://example.org/foo/bar/',
        ));

        $responseBlob = array(
            "HTTP/1.1 200 OK",
            "Content-Type: text/plain",
            "",
            "Hello there!"
        );

        $client->response = array(
            implode("\r\n", $responseBlob),
            array(
                'header_size' => 45,
                'http_code' => 200,
            ),
            CURLE_COULDNT_CONNECT,
            "Could not connect, or something" 
        );

        $caught = false;
        try {
            $client->request('POST', 'baz', 'sillybody', array('Content-Type' => 'text/plain'));
        } catch (Sabre_DAV_Exception $e) {
            $caught = true;
        }
        if (!$caught) {
            $this->markTestFailed('Exception was not thrown');
        }

    }

    function testRequestHTTPError() {

        $client = new Sabre_DAV_ClientMock(array(
            'baseUri' => 'http://example.org/foo/bar/',
        ));

        $responseBlob = array(
            "HTTP/1.1 400 Bad Request",
            "Content-Type: text/plain",
            "",
            "Hello there!"
        );

        $client->response = array(
            implode("\r\n", $responseBlob),
            array(
                'header_size' => 45,
                'http_code' => 400,
            ),
            0,
            "" 
        );

        $caught = false;
        try {
            $client->request('POST', 'baz', 'sillybody', array('Content-Type' => 'text/plain'));
        } catch (Sabre_DAV_Exception $e) {
            $caught = true;
        }
        if (!$caught) {
            $this->fail('Exception was not thrown');
        }

    }

    function testGetAbsoluteUrl() {

        $client = new Sabre_DAV_ClientMock(array(
            'baseUri' => 'http://example.org/foo/',
        ));

        $this->assertEquals(
            'http://example.org/foo/bar',
            $client->getAbsoluteUrl('bar')
        );

        $this->assertEquals(
            'http://example.org/bar',
            $client->getAbsoluteUrl('/bar')
        );

        $this->assertEquals(
            'http://example.com/bar',
            $client->getAbsoluteUrl('http://example.com/bar')
        );

    }

    function testOptions() {

        $client = new Sabre_DAV_ClientMock(array(
            'baseUri' => 'http://example.org/foo/bar/',
        ));

        $responseBlob = array(
            "HTTP/1.1 200 OK",
            "DAV: feature1, feature2",
            "",
        );

        $client->response = array(
            implode("\r\n", $responseBlob),
            array(
                'header_size' => 40,
                'http_code' => 200,
            ),
            0,
            "" 
        );

        $result = $client->options();
        $this->assertEquals(
            array('feature1', 'feature2'),
            $result
        );

    }

    function testOptionsNoDav() {

        $client = new Sabre_DAV_ClientMock(array(
            'baseUri' => 'http://example.org/foo/bar/',
        ));

        $responseBlob = array(
            "HTTP/1.1 200 OK",
            "",
        );

        $client->response = array(
            implode("\r\n", $responseBlob),
            array(
                'header_size' => 20,
                'http_code' => 200,
            ),
            0,
            "" 
        );

        $result = $client->options();
        $this->assertEquals(
            array(),
            $result
        );

    }

    /**
     * @expectedException InvalidArgumentException
     */
    function testPropFindNoXML() {

        $client = new Sabre_DAV_ClientMock(array(
            'baseUri' => 'http://example.org/foo/bar/',
        ));

        $responseBlob = array(
            "HTTP/1.1 200 OK",
            "",
        );

        $client->response = array(
            implode("\r\n", $responseBlob),
            array(
                'header_size' => 20,
                'http_code' => 200,
            ),
            0,
            "" 
        );

        $client->propfind('', array('{DAV:}foo','{DAV:}bar'));

    }

    function testPropFind() {

        $client = new Sabre_DAV_ClientMock(array(
            'baseUri' => 'http://example.org/foo/bar/',
        ));

        $responseBlob = array(
            "HTTP/1.1 200 OK",
            "",
            "<?xml version=\"1.0\"?>",
            "<d:multistatus xmlns:d=\"DAV:\">",
            "  <d:response>",
            "    <d:href>/foo/bar/</d:href>",
            "    <d:propstat>",
            "      <d:prop>",
            "         <d:foo>hello</d:foo>",
            "      </d:prop>",
            "      <d:status>HTTP/1.1 200 OK</d:status>",
            "    </d:propstat>",
            "    <d:propstat>",
            "      <d:prop>",
            "         <d:bar />",
            "      </d:prop>",
            "      <d:status>HTTP/1.1 404 Not Found</d:status>",
            "    </d:propstat>",
            "  </d:response>",
            "</d:multistatus>",
        );

        $client->response = array(
            implode("\r\n", $responseBlob),
            array(
                'header_size' => 19,
                'http_code' => 200,
            ),
            0,
            "" 
        );

        $result = $client->propfind('', array('{DAV:}foo','{DAV:}bar'));

        $this->assertEquals(array(
            '{DAV:}foo' => 'hello',
        ), $result);

        $requestBody = array(
            '<?xml version="1.0"?>',
            '<d:propfind xmlns:d="DAV:">',
            '  <d:prop>',
            '    <d:foo />',
            '    <d:bar />',
            '  </d:prop>',
            '</d:propfind>'
        );
        $requestBody = implode("\n", $requestBody);

        $this->assertEquals($requestBody, $client->curlSettings[CURLOPT_POSTFIELDS]);

    }

    function testPropFindDepth1CustomProp() {

        $client = new Sabre_DAV_ClientMock(array(
            'baseUri' => 'http://example.org/foo/bar/',
        ));

        $responseBlob = array(
            "HTTP/1.1 200 OK",
            "",
            "<?xml version=\"1.0\"?>",
            "<d:multistatus xmlns:d=\"DAV:\" xmlns:x=\"urn:custom\">",
            "  <d:response>",
            "    <d:href>/foo/bar/</d:href>",
            "    <d:propstat>",
            "      <d:prop>",
            "         <d:foo>hello</d:foo>",
            "         <x:bar>world</x:bar>",
            "      </d:prop>",
            "      <d:status>HTTP/1.1 200 OK</d:status>",
            "    </d:propstat>",
            "  </d:response>",
            "</d:multistatus>",
        );

        $client->response = array(
            implode("\r\n", $responseBlob),
            array(
                'header_size' => 19,
                'http_code' => 200,
            ),
            0,
            "" 
        );

        $result = $client->propfind('', array('{DAV:}foo','{urn:custom}bar'),1);

        $this->assertEquals(array(
            "/foo/bar/" => array(
                '{DAV:}foo' => 'hello',
                '{urn:custom}bar' => 'world',
            ),
        ), $result);

        $requestBody = array(
            '<?xml version="1.0"?>',
            '<d:propfind xmlns:d="DAV:">',
            '  <d:prop>',
            '    <d:foo />',
            '    <x:bar xmlns:x="urn:custom"/>',
            '  </d:prop>',
            '</d:propfind>'
        );
        $requestBody = implode("\n", $requestBody);

        $this->assertEquals($requestBody, $client->curlSettings[CURLOPT_POSTFIELDS]);

    }

    function testPropPatch() {

        $client = new Sabre_DAV_ClientMock(array(
            'baseUri' => 'http://example.org/foo/bar/',
        ));

        $responseBlob = array(
            "HTTP/1.1 200 OK",
            "",
        );

        $client->response = array(
            implode("\r\n", $responseBlob),
            array(
                'header_size' => 20,
                'http_code' => 200,
            ),
            0,
            "" 
        );

        $client->proppatch('', array(
            '{DAV:}foo' => 'newvalue',
            '{urn:custom}foo' => 'newvalue2',
            '{DAV:}bar' => null,
            '{urn:custom}bar' => null,
        ));

        $requestBody = array(
            '<?xml version="1.0"?>',
            '<d:propertyupdate xmlns:d="DAV:">',
            '<d:set><d:prop>',
            '    <d:foo>newvalue</d:foo>',
            '</d:prop></d:set>',
            '<d:set><d:prop>',
            '    <x:foo xmlns:x="urn:custom">newvalue2</x:foo>',
            '</d:prop></d:set>',
            '<d:remove><d:prop>',
            '    <d:bar />',
            '</d:prop></d:remove>',
            '<d:remove><d:prop>',
            '    <x:bar xmlns:x="urn:custom"/>',
            '</d:prop></d:remove>',
            '</d:propertyupdate>'
        );
        $requestBody = implode("\n", $requestBody);

        $this->assertEquals($requestBody, $client->curlSettings[CURLOPT_POSTFIELDS]);

    }

}
