<?php

namespace Baikal\Model\Structs;

/**
 * Struct that holds the Configuration parameters for LDAP authentication.
 *
 * @author El-Virus <elvirus@ilsrv.com>
 * @license http://sabre.io/license/ Modified GPL License
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
