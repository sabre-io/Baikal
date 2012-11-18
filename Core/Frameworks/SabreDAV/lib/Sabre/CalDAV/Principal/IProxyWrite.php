<?php

namespace Sabre\CalDAV\Principal;

use Sabre\DAVACL;

/**
 * ProxyWrite principal interface
 *
 * Any principal node implementing this interface will be picked up as a 'proxy
 * principal group'.
 *
 * @copyright Copyright (C) 2007-2012 Rooftop Solutions. All rights reserved.
 * @author Evert Pot (http://www.rooftopsolutions.nl/)
 * @license http://code.google.com/p/sabredav/wiki/License Modified BSD License
 */
interface IProxyWrite extends DAVACL\IPrincipal {

}
