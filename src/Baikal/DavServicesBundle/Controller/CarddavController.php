<?php

namespace Baikal\DavServicesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use
    Sabre\DAV,
    Sabre\CardDAV,
    Sabre\DAVACL;

class CarddavController extends Controller
{
    public function indexAction() {

        if($this->container->get('config.main')->getEnable_carddav() !== TRUE) {
            $response = new Response('CardDAV is disabled.');
            $response->setStatusCode('401', 'Unauthorized');
            return $response;
        }

        #var_dump($this->container->getParameter('dav_caldav_enabled'));

        $pdo = $this->container->get('doctrine.dbal.default_connection')->getWrappedConnection();
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        //Mapping PHP errors to exceptions
        $error_handler = function($errno, $errstr, $errfile, $errline ) {
            throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
        };

        set_error_handler($error_handler);

        // Backends
        $authBackend = new DAV\Auth\Backend\PDO($pdo);
        $principalBackend = new DAVACL\PrincipalBackend\PDO($pdo);
        $carddavBackend = new CardDAV\Backend\PDO($pdo);

        // Directory tree
        $tree = array(
            new DAVACL\PrincipalCollection($principalBackend),
            new CardDAV\AddressBookRoot($principalBackend, $carddavBackend)
        );

        // The object tree needs in turn to be passed to the server class
        $server = new DAV\Server($tree);

        // You are highly encouraged to set your WebDAV server base url. Without it,
        // SabreDAV will guess, but the guess is not always correct. Putting the
        // server on the root of the domain will improve compatibility.
        $server->setBaseUri($this->generateUrl("baikal_dav_services_carddav"));

        // Authentication plugin
        $authPlugin = new DAV\Auth\Plugin($authBackend, $this->container->getParameter('baikal.dav_realm'));
        $server->addPlugin($authPlugin);

        // CardDAV plugin
        $carddavPlugin = new CardDAV\Plugin();
        $server->addPlugin($carddavPlugin);

        // ACL plugin
        $aclPlugin = new DAVACL\Plugin();
        $server->addPlugin($aclPlugin);

        // Support for html frontend
        $browser = new DAV\Browser\Plugin();
        $server->addPlugin($browser);

        // And off we go!
        $server->exec();

        exit();
    }
}
