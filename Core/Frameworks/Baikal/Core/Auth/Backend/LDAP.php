<?php

namespace Baikal\Core\Auth\Backend;

/**
 * This is an authentication backend that uses LDAP.
 *
 * @copyright Copyright (C) Michel Stam
 * @author Michel Stam
 * @license http://sabre.io/license/ Modified BSD License
 */
class LDAP extends \Sabre\DAV\Auth\Backend\AbstractBasic {
    /**
     * LDAP server url in the form ldap(s)://host:port.
     *
     * @var string
     */
    protected $url;

    /**
     * LDAP base DN.
     *
     * @var string
     */
    protected $base;

    /**
     * LDAP admin DN (may be empty).
     *
     * @var string
     */
    protected $admin_dn;

    /**
     * LDAP admin password (may be empty).
     *
     * @var string
     */
    protected $admin_password;

    /**
     * LDAP attribute (user account; %u for username).
     *
     * @var string
     */
    protected $user_filter;

    /**
     * LDAP filter (groups).
     *
     * @var string
     */
    protected $group_filter;

    /**
     * LDAP attribute (groups).
     *
     * @var string
     */
    protected $group_attr;

    /**
     * Group name (required group membership).
     *
     * @var string
     */
    protected $group;

    /**
     * Creates the backend object. URL format: ldap(s)://host[:port]/.
     *
     * @param string $url
     * @param string $base
     * @param string $admin_dn
     * @param string $admin_password
     * @param string $usr_filter
     * @param string $grp_filter
     * @param string $grp_attr
     * @param string $group
     */
    public function __construct($url, $base, $adm_dn, $adm_pass, $usr_filter, $grp_filter, $grp_attr, $grp) {
        $this->url = $url;
        $this->base = $base;
        $this->admin_dn = $adm_dn;
        $this->admin_password = $adm_pass;
        $this->user_filter = $usr_filter;
        $this->group_filter = $grp_filter;
        $this->group_attr = $grp_attr;
        $this->group = $grp;
    }

    /**
     * Do an LDAP search and return the DN and attributes.
     *
     * @param string $handle
     * @param string $filt
     *
     * @return array
     */
    private function do_search($handle, $filt) {
        $success = false;
        $answer = [];

        $res = ldap_search($handle, $this->base, $filt, [$this->group_attr]);
        $success = $res && (ldap_count_entries($handle, $res) > 0);
        if ($success) {
            $entry = ldap_first_entry($handle, $res);
            $answer[] = ldap_get_dn($handle, $entry);
            $list = ldap_get_attributes($handle, $entry);
            if ($list && array_key_exists($this->group_attr, $list)) {
                unset($list[$this->group_attr]['count']);
                $answer[] = $list[$this->group_attr];
            } else {
                $answer[] = [];
            }

            ldap_free_result($res);
        }

        return $success ? $answer : false;
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
        $user_dn = false;
        $res = false;
        $group_dn = false;
        $success = false;
        $user_memberships = false;
        $group_members = false;

        try {
            $handle = ldap_connect($this->url);
            if ($handle && strlen($this->admin_dn)) {
                $success = ldap_bind($handle, $this->admin_dn, $this->admin_password);
            }

            // Find the user (anonymous)
            if ($handle) {
                $filter = str_replace('%u', $username, $this->user_filter);
                $res = $this->do_search($handle, $filter);
                $success = !empty($res);
            }

            if ($success) {
                $user_dn = $res[0];
                $user_memberships = $res[1];
            }

            // Authenticate user
            if ($success) {
                $tmp = ldap_connect($this->url);
                $success = (bool) $tmp;
                if ($success) {
                    $success = ldap_bind($tmp, $user_dn, $password);
                    ldap_close($tmp);
                }
            }

            // Locate the groups for the user
            if (strlen($this->group) > 0) {
                if ($success) {
                    $filter = str_replace('%g', $this->group, $this->group_filter);
                    $res = $this->do_search($handle, $filter);
                    $success = !empty($res);
                }

                if ($success) {
                    $group_dn = $res[0];
                    $group_members = $res[1];
                }

                // AD style groups
                if ($success && !empty($user_memberships)) {
                    $success = (array_search($group_dn, $user_memberships) !== false);
                }

                // POSIX style groups
                if ($success && !empty($group_members)) {
                    $success = (array_search($username, $group_members) !== false);

                    // Could also be a DN, not a group name
                    if (!$success) {
                        $success = (array_search($group_dn, $group_members) !== false);
                    }
                }
            }

            if ($handle) {
                ldap_close($handle);
            }
        } catch (\ErrorException $e) {
            error_log($e->getMessage());
            $success = false;
        }

        return $success;
    }
}
