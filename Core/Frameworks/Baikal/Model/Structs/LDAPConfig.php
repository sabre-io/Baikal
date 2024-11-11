<?php

#################################################################
#  Copyright notice
#
#  (c) 2022 El-Virus <elvirus@ilsrv.com>
#  All rights reserved
#
#  http://sabre.io/baikal
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

namespace Baikal\Model\Structs;

/**
 * Struct that holds the Configuration parameters for LDAP authentication.
 */
class LDAPConfig {
    public $ldap_mode;
    public $ldap_uri;
    public $ldap_bind_dn;
    public $ldap_bind_password;
    public $ldap_dn;
    public $ldap_cn;
    public $ldap_mail;
    public $ldap_search_base;
    public $ldap_search_attribute;
    public $ldap_search_filter;
    public $ldap_group;
}
