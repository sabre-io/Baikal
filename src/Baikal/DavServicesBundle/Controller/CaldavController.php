<?php

namespace Baikal\DavServicesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpFoundation\Request;

use Sabre\DAV\PropPatch,
    Sabre\DAV\INode;

class CaldavController extends Controller
{
    public function indexAction(Request $request) {

        if($this->container->get('config.main')->getEnable_caldav() !== TRUE) {
            $response = new Response('CalDAV is disabled.');
            $response->setStatusCode('401', 'Unauthorized');
            return $response;
        }
        
        $server = $this->container->get('baikal.davservice.calendar');

        # Injecting the current request in the Sabre Server
        $sabreRequest = \Sabre\HTTP\Sapi::createFromServerArray($GLOBALS['_SERVER']);
        $sabreRequest->setBody($request->getContent());
        $server->httpRequest = $sabreRequest;

        # Handling problem "Server answered 403 for CalDAVAccountRefreshQueueableOperation"
        $problem = function($path, PropPatch $proppatch) {
            $problemhandler = function($value) { return 200; };
            $proppatch->handle('{urn:ietf:params:xml:ns:caldav}default-alarm-vevent-date', $problemhandler);
            $proppatch->handle('{urn:ietf:params:xml:ns:caldav}default-alarm-vevent-datetime', $problemhandler);
        };

        $server->on('propPatch', $problem);

        // And off we go!
        $server->exec();

        exit();
    }
}
