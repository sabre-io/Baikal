<?php

namespace Baikal\Core;

/**
 * This is an authentication backend that uses ldap.
 *
 * @copyright Copyright (C) fruux GmbH (https://fruux.com/)
 * @author Aisha Tammy <aisha@bsd.ac>
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
     * LDAP server uri.
     * e.g. ldaps://ldap.example.org
     *
     * @var string
     */
    protected $ldap_uri;

    /*
     * LDAP dn pattern for binding
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

    /*
     * LDAP attribute to use for name
     *
     * @var string
     */
    protected $ldap_cn;

    /*
     * LDAP attribute used for mail
     *
     * @var string
     */
    protected $ldap_mail;

    /**
     * Creates the backend object.
     *
     * @param string $ldap_uri
     * @param string $ldap_dn
     * @param string $ldap_cn
     * @param string $ldap_mail
     *
     */
    public function __construct(\PDO $pdo, $table_name = 'users', $ldap_uri = 'ldap://127.0.0.1', $ldap_dn = 'mail=%u', $ldap_cn = 'cn', $ldap_mail = 'mail')
    {
        $this->pdo        = $pdo;
        $this->table_name = $table_name;
        $this->ldap_uri   = $ldap_uri;
        $this->ldap_dn    = $ldap_dn;
        $this->ldap_cn    = $ldap_cn;
        $this->ldap_mail  = $ldap_mail;
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

        $user_split = explode('@', $username, 2);
        $ldap_user = $user_split[0];
        $ldap_domain = '';
        if (count($user_split) > 1)
            $ldap_domain = $user_split[1];
        $domain_split = array_reverse(explode('.', $ldap_domain));

        $dn = str_replace('%u', $username, $this->ldap_dn);
        $dn = str_replace('%U', $ldap_user, $dn);
        $dn = str_replace('%d', $ldap_domain, $dn);
        for($i = 1; $i <= count($domain_split) and $i <= 9; $i++)
            $dn = str_replace('%' . $i, $domain_split[$i - 1], $dn);

        try {
            $bind = ldap_bind($conn, $dn, $password);
            if ($bind) {
                $success = true;
            }
        } catch (\ErrorException $e) {
            error_log($e->getMessage());
            error_log(ldap_error($conn));
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
