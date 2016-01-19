<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2013 Jérôme Schneider <mail@jeromeschneider.fr>
*  All rights reserved
*
*  http://baikal-server.com
*
*  This script is part of the Baïkal Server project. The Baïkal
*  Server project is free software; you can redistribute it
*  and/or modify it under the terms of the GNU General Public
*  License as published by the Free Software Foundation; either
*  version 2 of the License, or (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

ini_set("session.cookie_httponly", 1);
ini_set("display_errors", 0);
ini_set("log_errors", 1);

define("BAIKAL_CONTEXT", TRUE);
define("PROJECT_CONTEXT_BASEURI", "/");

if(file_exists(getcwd() . "/Core")) {
	# Flat FTP mode
	define("PROJECT_PATH_ROOT", getcwd() . "/");	#./
} else {
	# Dedicated server mode
	define("PROJECT_PATH_ROOT", dirname(getcwd()) . "/");	#../
}

if(!file_exists(PROJECT_PATH_ROOT . 'vendor/')) {
	die('<h1>Incomplete installation</h1><p>Ba&iuml;kal dependencies have not been installed. Please, execute "<strong>composer install</strong>" in the folder where you installed Ba&iuml;kal.');
}

require PROJECT_PATH_ROOT . 'vendor/autoload.php';

# Bootstraping Flake
\Flake\Framework::bootstrap();

# Bootstrapping Baïkal
\Baikal\Framework::bootstrap();

?><!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Baïkal server</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="CalDAV and CardDAV server for calendar and contact data based on PHP, SQLite/MySQL, and SabreDAV">
    <style type="text/css"><!--
      body {
        font: 14px/24px "HelveticaNeue", "Helvetica Neue", Helvetica, Arial, sans-serif; color: white;
        background: #005799;	/* median color for gradient */
        background: -webkit-radial-gradient(33%, ellipse cover, #338ACC 0%, #003863 100%);		/* Safari+Chrome */
        background: -moz-radial-gradient(center, ellipse cover, #338ACC 0%, #003863 100%);			/* Firefox */
        background-image: -ms-radial-gradient(center, ellipse cover, #338ACC 0%, #003863 100%);		/* IE9 */
        background: radial-gradient(center, ellipse cover, #338ACC 0%, #003863 100%);					/* CSS3 */
        display: block;
        padding: 50px 0 100%;
        overflow: hidden;
        position: relative;
        width: 724px;
        margin-right: auto;
        margin-left: auto;
      }
      button {
        font-size: 20px;
        font-weight:bold;
        padding: 8px 16px 8px;
        text-shadow: 0 1px 1px #003863;
      }
      h1 {
        font-weight: bold;
        letter-spacing: .4pt;
        font-size: 28px;
        line-height: 34px;
        max-width: 650px;
        margin-bottom: 20px;
        text-shadow: 0px 2px 3px #003863;
      }
      h3 {
        font-weight: bold;
        letter-spacing: .6pt;
        text-shadow: 0px 2px 6px #003863;
        font-size: 75px;
        text-rendering: optimizelegibility;
        margin: 0px;
      }
      p {
        font-weight: bold;
        font-size: 18px;
        line-height: 34px;
        max-width: 650px;
        margin-bottom: 20px;
        text-shadow: 0px 2px 3px #003863;
      }
      div {
        float: left;
        margin-left: 20px;
      }
    --></style>
  </head>
  <body>
    <div style="margin-left: -40px;">
      <div style="width: 228px;">
        <img src="data:image/png;base64,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" alt="Baïkal logo" style="max-height: 228px; max-width: 100%; vertical-align: middle; border: 0 none;">
      </div>
      <div style="width: 476px;">
        <p style="line-height: 0; margin-bottom: 0;">&nbsp;</p>
        <h3>Baïkal</h3>
        <p>Lightweight CalDAV+CardDAV server</p>
        <h1>Baïkal is running allright.</h1>
        <button style="color:#005799;" onclick="location.href='http://baikal-server.com/'" >Get your own!</button>&nbsp; &nbsp;<button style="color:#d9150b;" onclick="location.href='admin/'" >Admin page</button>
      </div>
    </div>
  </body>
</html>
