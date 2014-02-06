<?php

namespace Baikal\Core;

/**
 * This is an authentication backend that uses a ldap backend to authenticate user.
 *
 * @author Sascha Kuehndel (InuSasha) <dev@inusasha.de>
 * @license http://code.google.com/p/sabredav/wiki/License Modified BSD License
 */
class LDAPUserBindAuth extends AbstractExternalAuth {

    /**
     * AccountValues for getAccountValues
     * 
     * @var array ('displayname' => string, 'email' => string)
     */
    private $accountValues;

    /**
     * Creates the backend object.
     *
     * If the filename argument is passed in, it will parse out the specified file fist.
     *
     * @param PDO $pdo
     * @param string $tableName The PDO table name to use
     */
    public function __construct(\PDO $pdo, $tableName = NULL) {
        parent::__construct($pdo, $tableName);
    }

    /**
     * Validates a username and password over ldap
     *
     * @param string $username
     * @param string $password
     * @return bool
     */
    public function validateUserPassExternal($username, $password) {

        /* create ldap connection */
        $conn = ldap_connect(BAIKAL_DAV_LDAP_URI);
        if (!$conn)
          return false;
        if (!ldap_set_option($conn,LDAP_OPT_PROTOCOL_VERSION,3))
          return false;

        /* bind with user */
        $arr = explode('@', $username, 2);
        $dn = str_replace('%n', $username, BAIKAL_DAV_LDAP_DN_TEMPLATE);
        $dn = str_replace('%u', $arr[0], $dn);
        if(isset($arr[1])) $dn = str_replace('%d', $arr[1], $dn);         
        $bind = ldap_bind($conn, $dn, $password);
        if (!$bind) {
             ldap_close($conn);
             return false;
        }

        /* read displayname and email from user */
        $this->accountValues = array();
        $sr = ldap_read($conn, $dn, '(objectclass=*)', array('sn','mail'));
        $entry = ldap_get_entries($conn, $sr);
        if (isset($entry[0]['sn'][0]))
             $this->accountValues['displayname'] = $entry[0]['sn'][0];
        if (isset($entry[0]['mail'][0]))
             $this->accountValues['email'] = $entry[0]['mail'][0];

        /* close */
        ldap_close($conn);
        return true;
    }

    public function getAccountValues($username) {

        return $this->accountValues;
    }

}
