<?php

class Sabre_DAVACL_MockPrincipalBackend implements Sabre_DAVACL_IPrincipalBackend {

    public $groupMembers = array();

    function getPrincipalsByPrefix($prefix) {

        if ($prefix=='principals') {

            return array(
                array(
                    'uri' => 'principals/user1',
                    '{DAV:}displayname' => 'User 1',
                    '{http://sabredav.org/ns}email-address' => 'user1.sabredav@sabredav.org',
                ),
                array(
                    'uri' => 'principals/admin',
                    '{DAV:}displayname' => 'Admin',
                ),
            );

         }

    }

    function getPrincipalByPath($path) {

        foreach($this->getPrincipalsByPrefix('principals') as $principal) {
            if ($principal['uri'] === $path) return $principal;
        }

    }

    function getGroupMemberSet($path) {

        return isset($this->groupMembers[$path]) ? $this->groupMembers[$path] : array();

    }

    function getGroupMembership($path) {

        $membership = array();
        foreach($this->groupMembers as $group=>$members) {
            if (in_array($path, $members)) $membership[] = $group;
        } 
        return $membership; 

    }

    function setGroupMemberSet($path, array $members) {

        $this->groupMembers[$path] = $members;

    }

}
