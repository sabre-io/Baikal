<?php

namespace Baikal\DavServicesBundle\Service\DAV;

use Sabre\DAV\Server as DAVServer,
    Sabre\DAVACL\PrincipalCollection,
    Sabre\DAVACL\PrincipalBackend\AbstractBackend as AbstractPrincipalBackend,
    Sabre\CalDAV\CalendarRootNode,
    Sabre\CalDAV\Backend\AbstractBackend as AbstractCalendarBackend;

class CalendarServiceFactory {

    protected $principalBackend;
    protected $calendarBackend;
    protected $router;
    protected $rootRouteName;
    protected $davplugins = array();

    public function __construct(
        AbstractPrincipalBackend $principalBackend,
        AbstractCalendarBackend $calendarBackend,
        $router,
        $rootRouteName,
        array $davplugins = array()
    ) {
        $this->principalBackend = $principalBackend;
        $this->calendarBackend = $calendarBackend;
        $this->router = $router;
        $this->rootRouteName = $rootRouteName;
        $this->davplugins = $davplugins;
    }

    public function get() {

        // Directory tree
        $tree = array(
            new PrincipalCollection($this->principalBackend),
            new CalendarRootNode($this->principalBackend, $this->calendarBackend)
        );

        // The object tree needs in turn to be passed to the server class
        $server = new DAVServer($tree);

        // You are highly encouraged to set your WebDAV server base url. Without it,
        // SabreDAV will guess, but the guess is not always correct. Putting the
        // server on the root of the domain will improve compatibility.
        $server->setBaseUri($this->router->generate($this->rootRouteName));

        foreach($this->davplugins as $davplugin) {
            $server->addPlugin($davplugin);
        }

        return $server;
    }
}