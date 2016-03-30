<?php
#################################################################
#  Copyright notice
#
#  (c) 2013 Jérôme Schneider <mail@jeromeschneider.fr>
#  All rights reserved
#
#  http://baikal-server.com
#
#  This script is part of the Baïkal Server project. The Baïkal
#  Server project is free software; you can redistribute it
#  and/or modify it under the terms of the GNU General Public
#  License as published by the Free Software Foundation; either
#  version 2 of the License, or (at your option) any later version.
#
#  The GNU General Public License can be found at
#  http://www.gnu.org/copyleft/gpl.html.
#
#  This script is distributed in the hope that it will be useful,
#  but WITHOUT ANY WARRANTY; without even the implied warranty of
#  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#  GNU General Public License for more details.
#
#  This copyright notice MUST APPEAR in all copies of the script!
#################################################################


namespace Baikal\Core;

use PDO;

/**
 * The Baikal Server
 *
 * This class sets up the underlying Sabre\DAV\Server object.
 *
 * @copyright Copyright (C) Jérôme Schneider <mail@jeromeschneider.fr>
 * @author Evert Pot (http://evertpot.com/)
 * @license http://sabre.io/license/ GPLv2
 */
class Server {

    /**
     * Is CalDAV enabled?
     *
     * @var bool
     */
    protected $enableCalDAV;

    /**
     * is CardDAV enabled?
     *
     * @var bool
     */
    protected $enableCardDAV;

    /**
     * "Basic" or "Digest"
     *
     * @var string
     */
    protected $authType;

    /**
     * HTTP authentication realm
     *
     * @var string
     */
    protected $authRealm;

    /**
     * Reference to Database object
     *
     * @var PDO
     */
    protected $pdo;

    /**
     * baseUri for the sabre/dav server
     *
     * @var string
     */
    protected $baseUri;

    /**
     * The sabre/dav Server object
     *
     * @var \Sabre\DAV\Server
     */
    protected $server;


    /**
     * Creates the server object.
     *
     * @param bool $enableCalDAV
     * @param bool $enableCardDAV
     * @param string $authType
     * @param string $authRealm
     * @param PDO $pdo
     * @param string $baseUri
     */
    function __construct($enableCalDAV, $enableCardDAV, $authType, $authRealm, PDO $pdo, $baseUri) {

        $this->enableCalDAV = $enableCalDAV;
        $this->enableCardDAV = $enableCardDAV;
        $this->authType = $authType;
        $this->authRealm = $authRealm;
        $this->pdo = $pdo;
        $this->baseUri = $baseUri;

        $this->initServer();

    }

    /**
     * Starts processing
     *
     * @return void
     */
    function start() {

        $this->server->exec();

    }

    /**
     * Initializes the server object
     *
     * @return void
     */
    protected function initServer() {

        if ($this->authType === 'Basic') {
            $authBackend = new \Baikal\Core\PDOBasicAuth($this->pdo, $this->authRealm);
        } else {
            $authBackend = new \Sabre\DAV\Auth\Backend\PDO($this->pdo);
            $authBackend->setRealm($this->authRealm);
        }
        $principalBackend = new \Sabre\DAVACL\PrincipalBackend\PDO($this->pdo);

        $nodes = [
            new \Sabre\CalDAV\Principal\Collection($principalBackend)
        ];
        if ($this->enableCalDAV) {
            $calendarBackend = new \Sabre\CalDAV\Backend\PDO($this->pdo);
            $nodes[] = new \Sabre\CalDAV\CalendarRoot($principalBackend, $calendarBackend);
        }
        if ($this->enableCardDAV) {
            $carddavBackend = new \Sabre\CardDAV\Backend\PDO($this->pdo);
            $nodes[] = new \Sabre\CardDAV\AddressBookRoot($principalBackend, $carddavBackend);
        }

        $this->server = new \Sabre\DAV\Server($nodes);
        $this->server->setBaseUri($this->baseUri);

        $this->server->addPlugin(new \Sabre\DAV\Auth\Plugin($authBackend, $this->authRealm));
        $this->server->addPlugin(new \Sabre\DAVACL\Plugin());
        $this->server->addPlugin(new \Sabre\DAV\Browser\Plugin());

        $this->server->addPlugin(new \Sabre\DAV\PropertyStorage\Plugin(
            new \Sabre\DAV\PropertyStorage\Backend\PDO($this->pdo)
        ));

        // WebDAV-Sync!
        $this->server->addPlugin(new \Sabre\DAV\Sync\Plugin());

        if ($this->enableCalDAV) {
            $this->server->addPlugin(new \Sabre\CalDAV\Plugin());
            $this->server->addPlugin(new \Sabre\CalDAV\ICSExportPlugin());
            $this->server->addPlugin(new \Sabre\CalDAV\Schedule\Plugin());
        }
        if ($this->enableCardDAV) {
            $this->server->addPlugin(new \Sabre\CardDAV\Plugin());
            $this->server->addPlugin(new \Sabre\CardDAV\VCFExportPlugin());
        }

    }

}
