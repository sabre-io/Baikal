<?php

#################################################################
#  Copyright notice
#
#  (c) 2013 Jérôme Schneider <mail@jeromeschneider.fr>
#  All rights reserved
#
#  http://flake.codr.fr
#
#  This script is part of the Flake project. The Flake
#  project is free software; you can redistribute it
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

namespace Flake\Controller;

class Rpc extends \Flake\Core\Render\Container {
    function initializeContext() {
        $this->injectHTTPHeaders();
        $GLOBALS["POSTCONNECTIONSERVICES"] = [];
    }

    function injectHTTPHeaders() {
        ob_start();

        header("Access-Control-Allow-Origin: *");    # To allow cross domain AJAX response
        header("Access-Control-Allow-Credentials: true");    # To allow cross domain cookies
        header("Content-Type: application/json; charset=UTF-8");

        # Needed to cut client off when needed
        header("Connection: close\r\n");
        ignore_user_abort(true);
    }

    function P3PAllowCrossDomainCookies() {
        # This tells IE6+ to accept passing cookies allong when establishing a XHR connection to read.codr.fr
        header('P3P: CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');
    }

    function sendResponseCutClientAndRunPostConnectionTasks() {
        header("Content-Length: " . ob_get_length());
        ob_end_flush();
        flush();

        reset($GLOBALS["POSTCONNECTIONSERVICES"]);

        # If post-connection services are registered, process
        foreach ($GLOBALS["POSTCONNECTIONSERVICES"] as $service) {
            $service->execute();
        }

        session_write_close();
    }
}
