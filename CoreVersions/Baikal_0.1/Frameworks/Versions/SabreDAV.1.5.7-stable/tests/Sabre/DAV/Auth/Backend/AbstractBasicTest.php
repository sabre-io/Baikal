<?php

require_once 'Sabre/HTTP/ResponseMock.php';

class Sabre_DAV_Auth_Backend_AbstractBasicTest extends PHPUnit_Framework_TestCase {

    /**
     * @expectedException Sabre_DAV_Exception_NotAuthenticated
     */
    public function testAuthenticateNoHeaders() {

        $response = new Sabre_HTTP_ResponseMock();
        $tree = new Sabre_DAV_ObjectTree(new Sabre_DAV_SimpleDirectory('bla'));
        $server = new Sabre_DAV_Server($tree);
        $server->httpResponse = $response;

        $backend = new Sabre_DAV_Auth_Backend_AbstractBasicMock();
        $backend->authenticate($server,'myRealm');

    }

    /**
     * @expectedException Sabre_DAV_Exception_NotAuthenticated
     */
    public function testAuthenticateUnknownUser() {

        $response = new Sabre_HTTP_ResponseMock();
        $tree = new Sabre_DAV_ObjectTree(new Sabre_DAV_SimpleDirectory('bla'));
        $server = new Sabre_DAV_Server($tree);
        $server->httpResponse = $response;

        $request = new Sabre_HTTP_Request(array(
            'PHP_AUTH_USER' => 'username',
            'PHP_AUTH_PW' => 'wrongpassword',
        ));
        $server->httpRequest = $request;

        $backend = new Sabre_DAV_Auth_Backend_AbstractBasicMock();
        $backend->authenticate($server,'myRealm');

    }

    public function testAuthenticate() {

        $response = new Sabre_HTTP_ResponseMock();
        $tree = new Sabre_DAV_ObjectTree(new Sabre_DAV_SimpleDirectory('bla'));
        $server = new Sabre_DAV_Server($tree);
        $server->httpResponse = $response;

        $request = new Sabre_HTTP_Request(array(
            'PHP_AUTH_USER' => 'username',
            'PHP_AUTH_PW' => 'password',
        ));
        $server->httpRequest = $request;

        $backend = new Sabre_DAV_Auth_Backend_AbstractBasicMock();
        $this->assertTrue($backend->authenticate($server,'myRealm'));

        $result = $backend->getCurrentUser();

        $this->assertEquals('username', $result);

    }


}


class Sabre_DAV_Auth_Backend_AbstractBasicMock extends Sabre_DAV_Auth_Backend_AbstractBasic {

    function validateUserPass($username, $password) {

        return ($username == 'username' && $password == 'password');

    }

}
