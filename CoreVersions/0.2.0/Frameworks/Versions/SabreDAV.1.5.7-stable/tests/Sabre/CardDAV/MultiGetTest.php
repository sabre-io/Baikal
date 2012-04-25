<?php

require_once 'Sabre/HTTP/ResponseMock.php';

class Sabre_CardDAV_MultiGetTest extends Sabre_CardDAV_AbstractPluginTest {

    function testMultiGet() {

        $request = new Sabre_HTTP_Request(array(
            'REQUEST_METHOD' => 'REPORT',
            'REQUEST_URI' => '/addressbooks/user1/book1',
        ));

        $request->setBody(
'<?xml version="1.0"?>
<c:addressbook-multiget xmlns:d="DAV:" xmlns:c="urn:ietf:params:xml:ns:carddav">
    <d:prop>
      <d:getetag />
      <c:address-data />
    </d:prop>
    <d:href>/addressbooks/user1/book1/card1</d:href>
</c:addressbook-multiget>'
            );

        $response = new Sabre_HTTP_ResponseMock();

        $this->server->httpRequest = $request;
        $this->server->httpResponse = $response;

        $this->server->exec();

        $this->assertEquals('HTTP/1.1 207 Multi-Status', $response->status, 'Incorrect status code. Full response body:' . $response->body);

        // using the client for parsing
        $client = new Sabre_DAV_Client(array('baseUri'=>'/'));

        $result = $client->parseMultiStatus($response->body);

        $this->assertEquals(array(
            '/addressbooks/user1/book1/card1' => array(
                200 => array(
                    '{DAV:}getetag' => '"' . md5("BEGIN:VCARD\nVERSION:3.0\nUID:12345\nEND:VCARD") . '"',
                    '{urn:ietf:params:xml:ns:carddav}address-data' => "BEGIN:VCARD\nVERSION:3.0\nUID:12345\nEND:VCARD",
                )
            )
        ), $result);

    }

}
