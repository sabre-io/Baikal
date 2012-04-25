<?php

class Sabre_DAVACL_MockPrincipal extends Sabre_DAV_Node implements Sabre_DAVACL_IPrincipal {

    public $name;
    public $principalUrl;
    public $groupMembership = array();
    public $groupMemberSet = array();

    function __construct($name,$principalUrl,array $groupMembership = array(), array $groupMemberSet = array()) {

        $this->name = $name;
        $this->principalUrl = $principalUrl;
        $this->groupMembership = $groupMembership;
        $this->groupMemberSet = $groupMemberSet;

    }

    function getName() {

        return $this->name;

    }

    function getDisplayName() {

        return $this->getName();

    }

    function getAlternateUriSet() {

        return array();

    }

    function getPrincipalUrl() {

        return $this->principalUrl;

    }

    function getGroupMemberSet() {

        return $this->groupMemberSet;

    }

    function getGroupMemberShip() {

        return $this->groupMembership;

    }

    function setGroupMemberSet(array $groupMemberSet) {

        $this->groupMemberSet = $groupMemberSet;

    }
}

