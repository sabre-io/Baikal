<?php

namespace Baikal\Core;

/**
 * This is an authentication backend that uses ldap.
 *
 * @copyright Copyright (C) fruux GmbH (https://fruux.com/)
 * @author Aisha Tammy <aisha@bsd.ac>
 * @author El-Virus <elvirus@ilsrv.com>
 * @license http://sabre.io/license/ Modified BSD License
 */
class LDAP extends \Sabre\DAV\Auth\Backend\AbstractBasic
{
    /**
     * Reference to PDO connection.
     *
     * @var PDO
     */
    protected $pdo;

    /**
     * PDO table name we'll be using.
     *
     * @var string
     */
    protected $table_name;

    /**
     * LDAP mode.
     * Defines if LDAP authentication should match
     * by DN, Attribute, or Filter.
     * 
     * @var string
     */
    protected $ldap_mode;

    /**
     * LDAP server uri.
     * e.g. ldaps://ldap.example.org
     *
     * @var string
     */
    protected $ldap_uri;

    /**
     * LDAP bind dn.
     * Defines the bind dn that Baikal is going to use
     * when looking for an attribute or filtering.
     * 
     * @var string
     */
    protected $ldap_bind_dn;

    /**
     * LDAP bind password.
     * Defines the password used by Baikal for binding.
     * 
     * @var string
     */
    protected $ldap_bind_password;

     /**
     * LDAP dn pattern for binding.
     *
     * %u   - gets replaced by full username
     * %U   - gets replaced by user part when the
     *        username is an email address
     * %d   - gets replaced by domain part when the
     *        username is an email address
     * %1-9 - gets replaced by parts of the the domain
     *        split by '.' in reverse order
     *        mail.example.org: %1 = org, %2 = example, %3 = mail
     *
     * @var string
     */
    protected $ldap_dn;

    /**
     * LDAP attribute to use for name.
     *
     * @var string
     */
    protected $ldap_cn;

    /**
     * LDAP attribute used for mail.
     *
     * @var string
     */
    protected $ldap_mail;

    /**
     * LDAP base path where to search for attributes
     * and apply filters.
     * 
     * @var string
     */
    protected $ldap_search_base;

    /**
     * LDAP attribute to search for.
     * 
     * @var string
     */
    protected $ldap_search_attribute;

    /**
     * LDAP filter to apply.
     * 
     * @var string
     */
    protected $ldap_search_filter;

    /**
     * LDAP group to check if a user is member of.
     * 
     * @var string
     */
    protected $ldap_group;

    /**
     * Replaces patterns for their assigned value.
     * 
     * @param string &$base
     * @param string $username
     * 
     */

    protected function patternReplace(&$base, $username)
    {
        $user_split = explode('@', $username, 2);
        $ldap_user = $user_split[0];
        $ldap_domain = '';
        if (count($user_split) > 1)
            $ldap_domain = $user_split[1];
        $domain_split = array_reverse(explode('.', $ldap_domain));

        $base = str_replace('%u', $username, $base);
        $base = str_replace('%U', $ldap_user, $base);
        $base = str_replace('%d', $ldap_domain, $base);
        for($i = 1; $i <= count($domain_split) and $i <= 9; $i++)
            $base = str_replace('%' . $i, $domain_split[$i - 1], $base);
    }

    /**
     * Checks if a user can bind with a password.
     * If an error is produced, it will be logged.
     * 
     * @param \LDAP\Connection &$conn
     * @param string $dn
     * @param string $password
     * 
     * @return bool
     */

    protected function doesBind(&$conn, $dn, $password)
    {
        try {
            $bind = ldap_bind($conn, $dn, $password);
            if ($bind)
                return true;
        } catch (\ErrorException $e) {
            error_log($e->getMessage());
            error_log(ldap_error($conn));
        }
        return false;
    }

    /**
     * Creates the backend object.
     *
     * @param string $ldap_uri
     * @param string $ldap_dn
     * @param string $ldap_cn
     * @param string $ldap_mail
     *
     */
    public function __construct(\PDO $pdo, $table_name = 'users', $ldap_mode = 'DN', $ldap_uri = 'ldap://127.0.0.1', $ldap_bind_dn = 'cn=baikal,ou=apps,dc=example,dc=com', $ldap_bind_password = '', $ldap_dn = 'mail=%u', $ldap_cn = 'cn', $ldap_mail = 'mail', $ldap_search_base = 'ou=users,dc=example,dc=com', $ldap_search_attribute = 'uid=%U', $ldap_search_filter = '(objectClass=*)', $ldap_group = 'cn=baikal,ou=groups,dc=example,dc=com')
    {
        $this->pdo                   = $pdo;
        $this->table_name            = $table_name;
        $this->ldap_mode             = $ldap_mode;
        $this->ldap_uri              = $ldap_uri;
        $this->ldap_bind_dn          = $ldap_bind_dn;
        $this->ldap_bind_password    = $ldap_bind_password;
        $this->ldap_dn               = $ldap_dn;
        $this->ldap_cn               = $ldap_cn;
        $this->ldap_mail             = $ldap_mail;
        $this->ldap_search_base      = $ldap_search_base;
        $this->ldap_search_attribute = $ldap_search_attribute;
        $this->ldap_search_filter    = $ldap_search_filter;
        $this->ldap_group            = $ldap_group;
    }

    /**
     * Connects to an LDAP server and tries to authenticate.
     *
     * @param string $username
     * @param string $password
     *
     * @return bool
     */
    protected function ldapOpen($username, $password)
    {
        $conn = ldap_connect($this->ldap_uri);
        if(!$conn)
            return false;
        if(!ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, 3))
            return false;

        $success = false;

        if ($this->ldap_mode == 'DN') {
            $this->patternReplace($dn, $username);

            $success = $this->doesBind($conn, $dn, $password);
        } elseif ($this->ldap_mode == 'Attribute' || $this->ldap_mode == 'Group') {
            try {
                if (!$this->doesBind($conn, $this->ldap_bind_dn, $this->ldap_bind_password)) {
                    return false;
                }

                $attribute = $this->ldap_search_attribute;
                $this->patternReplace($attribute, $username);

                $result = ldap_get_entries($conn, ldap_search($conn, $this->ldap_search_base, '('.$attribute.')', [explode('=', $attribute, 2)[0]], 0, 1, 0, LDAP_DEREF_ALWAYS, []))[0];
                
                $dn = $result["dn"];

                if ($this->ldap_mode == 'Group') {
                    $inGroup = FALSE;
                    $members = ldap_get_entries($conn, ldap_read($conn, $this->ldap_group, '(objectClass=*)', ['member', 'uniqueMember'], 0, 0, 0, LDAP_DEREF_NEVER, []))[0];
                    if (isset($members["member"])) {
                        foreach ($members["member"] as $member) {
                            if ($member == $result["dn"]) {
                                $inGroup = TRUE;
                                break;
                            }
                        }
                    }
                    if (isset($members["uniqueMember"])) {
                        foreach ($members["uniqueMember"] as $member) {
                            if ($member == $result["dn"]) {
                                $inGroup = TRUE;
                                break;
                            }
                        }
                    }
                    if (!$inGroup)
                        return false;
                }

                $success = $this->doesBind($conn, $dn, $password);
            } catch (\ErrorException $e) {
                error_log($e->getMessage());
                error_log(ldap_error($conn));
            }
        } elseif ($this->ldap_mode == 'Filter') {
            try {
                if (!$this->doesBind($conn, $this->ldap_bind_dn, $this->ldap_bind_password)) {
                    return false;
                }

                $filter = $this->ldap_search_filter;
                $this->patternReplace($filter, $username);

                $result = ldap_get_entries($conn, ldap_search($conn, $this->ldap_search_base, $filter, [], 0, 1, 0, LDAP_DEREF_ALWAYS, []))[0];

                $dn = $result["dn"];
                $success = $this->doesBind($conn, $dn, $password);
            } catch (\ErrorException $e) {
                error_log($e->getMessage());
                error_log(ldap_error($conn));
            }
        } else {
            error_log('Unknown LDAP authentication mode');
        }

        if($success){
            $stmt = $this->pdo->prepare('SELECT username, digesta1 FROM ' . $this->table_name . ' WHERE username = ?');
            $stmt->execute([$username]);
            $result = $stmt->fetchAll();

            if (empty($result)) {
                $search_results = ldap_read($conn, $dn, '(objectclass=*)', array($this->ldap_cn, $this->ldap_mail));
                $entry = ldap_get_entries($conn, $search_results);
                $user_displayname = $username;
                $user_email = 'unset-email';
                if (!empty($entry[0][$this->ldap_cn]))
                    $user_displayname = $entry[0][$this->ldap_cn][0];
                if (!empty($entry[0][$this->ldap_mail]))
                    $user_email = $entry[0][$this->ldap_mail][0];

                $user = new \Baikal\Model\User();
                $user->set('username', $username);
                $user->set('displayname', $user_displayname);
                $user->set('email', $user_email);
                $user->persist();
            }
        }

        ldap_close($conn);

        return $success;
    }

    /**
     * Validates a username and password by trying to authenticate against LDAP.
     *
     * @param string $username
     * @param string $password
     *
     * @return bool
     */
    protected function validateUserPass($username, $password)
    {
        return $this->ldapOpen($username, $password);
    }
}
