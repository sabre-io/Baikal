<?php

#################################################################
#  Copyright notice
#
#  (c) 2020 Michel Stam <michel@reverze.net>
#  All rights reserved
#
#  http://sabre.io/baikal
#
#  This script is part of the Baïal Server project. The Baïkal
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

/*
 * Baikal Server automatic user creation plugin.
 *
 * @copyright Copyright (C) Michel Stam <michel@reverze.net>
 * @license http://sabre.io/license/ GPLv2
 */

use Sabre\DAV\Server;
use Sabre\DAV\ServerPlugin;

class AutoUser extends ServerPlugin {
    private $authPlugin;
    private $server;

    function __construct($authPlugin) {
        $this->authPlugin = $authPlugin;
    }

    public function initialize(Server $server) {
        $this->server = $server;
        $server->on('beforeMethod:*', [$this, 'beforeMethod'], 11);
    }

    function getPluginName() {
        return 'autouser';
    }

    public function beforeMethod(\Sabre\HTTP\Request $request) {
        $principal = $this->authPlugin->getCurrentPrincipal();
        if ($principal === null) {
            return;
        }

        $username = explode('/', $principal);
        if ($this->server->tree->nodeExists($principal)) {
            return;
        }

        $oUser = new \Baikal\Model\User(false);
        $oUser->set("username", array_pop($username));
        $oUser->persist();
    }

    public function getPluginInfo() {
        return [
            'name' => $this->getPluginName(),
            'description' => 'Automatically creates users',
        ];
    }
}
