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
class LDAP extends \Sabre\DAV\Auth\Backend\AbstractBasic {
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
     * e.g. "ldaps://ldap.example.org".
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
     * Replaces patterns for their assigned value using the
     * given username, using cyrus-sasl style replacements.
     *
     * %u   - gets replaced by full username
     * %U   - gets replaced by user part when the
     *        username is an email address
     * %d   - gets replaced by domain part when the
     *        username is an email address
     * %%   - gets replaced by %
     * %1-9 - gets replaced by parts of the the domain
     *        split by '.' in reverse order
     *
     * full example for jane.doe@mail.example.org:
     *        %u = jane.doe@mail.example.org
     *        %U = jane.doe
     *        %d = mail.example.org
     *        %1 = org
     *        %2 = example
     *        %3 = mail
     *
     * @param string $line
     * @param string $username
     *
     * @return string
     */
    protected function patternReplace($line, $username) {
        $user_split = [$username];
        $user = $username;
        $domain = '';
        try {
            $user_split = explode('@', $username, 2);
            $user = $user_split[0];
            if (2 == count($user_split)) {
                $domain = $user_split[1];
            }
        } catch (Exception $ignored) {
        }
        $domain_split = [];
        try {
            $domain_split = array_reverse(explode('.', $domain));
        } catch (Exception $ignored) {
            $domain_split = [];
        }

        $parsed_line = '';
        for ($i = 0; $i < strlen($line); ++$i) {
            if ('%' == $line[$i]) {
                ++$i;
                $next_char = $line[$i];
                if ('u' == $next_char) {
                    $parsed_line .= $username;
                } elseif ('U' == $next_char) {
                    $parsed_line .= $user;
                } elseif ('d' == $next_char) {
                    $parsed_line .= $domain;
                } elseif ('%' == $next_char) {
                    $parsed_line .= '%';
                } else {
                    for ($j = 1; $j <= count($domain_split) && $j <= 9; ++$j) {
                        if ($next_char == '' . $j) {
                            $parsed_line .= $domain_split[$j - 1];
                        }
                    }
                }
            } else {
                $parsed_line .= $line[$i];
            }
        }

        return $parsed_line;
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
    protected function doesBind(&$conn, $dn, $password) {
        try {
            $bind = ldap_bind($conn, $dn, $password);
            if ($bind) {
                return true;
            }
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
     */
    public function __construct(\PDO $pdo, $table_name, $ldap_mode, $ldap_uri, $ldap_bind_dn, $ldap_bind_password, $ldap_dn, $ldap_cn, $ldap_mail, $ldap_search_base, $ldap_search_attribute, $ldap_search_filter, $ldap_group) {
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
    protected function ldapOpen($username, $password) {
        $conn = ldap_connect($this->ldap_uri);
        if (!$conn) {
            return false;
        }
        if (!ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, 3)) {
            return false;
        }

        $success = false;

        if ($this->ldap_mode == 'DN') {
            $dn = $this->patternReplace($dn, $username);

            $success = $this->doesBind($conn, $dn, $password);
        } elseif ($this->ldap_mode == 'Attribute' || $this->ldap_mode == 'Group') {
            try {
                if (!$this->doesBind($conn, $this->ldap_bind_dn, $this->ldap_bind_password)) {
                    return false;
                }

                $attribute = $this->ldap_search_attribute;
                $attribute = $this->patternReplace($attribute, $username);

                $result = ldap_get_entries($conn, ldap_search($conn, $this->ldap_search_base, '(' . $attribute . ')',
                    [explode('=', $attribute, 2)[0]], 0, 1, 0, LDAP_DEREF_ALWAYS, []))[0];

                $dn = $result["dn"];

                if ($this->ldap_mode == 'Group') {
                    $inGroup = false;
                    $members = ldap_get_entries($conn, ldap_read($conn, $this->ldap_group, '(objectClass=*)',
                        ['member', 'uniqueMember'], 0, 0, 0, LDAP_DEREF_NEVER, []))[0];
                    if (isset($members["member"])) {
                        foreach ($members["member"] as $member) {
                            if ($member == $result["dn"]) {
                                $inGroup = true;
                                break;
                            }
                        }
                    }
                    if (isset($members["uniqueMember"])) {
                        foreach ($members["uniqueMember"] as $member) {
                            if ($member == $result["dn"]) {
                                $inGroup = false;
                                break;
                            }
                        }
                    }
                    if (!$inGroup) {
                        return false;
                    }
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
                $filter = $this->patternReplace($filter, $username);

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

        if ($success) {
            $stmt = $this->pdo->prepare('SELECT username, digesta1 FROM ' . $this->table_name . ' WHERE username = ?');
            $stmt->execute([$username]);
            $result = $stmt->fetchAll();

            if (empty($result)) {
                $search_results = ldap_read($conn, $dn, '(objectclass=*)', [$this->ldap_cn, $this->ldap_mail]);
                $entry = ldap_get_entries($conn, $search_results);
                $user_displayname = $username;
                $user_email = 'unset-email';
                if (!empty($entry[0][$this->ldap_cn])) {
                    $user_displayname = $entry[0][$this->ldap_cn][0];
                }
                if (!empty($entry[0][$this->ldap_mail])) {
                    $user_email = $entry[0][$this->ldap_mail][0];
                }

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
    protected function validateUserPass($username, $password) {
        return $this->ldapOpen($username, $password);
    }
}
