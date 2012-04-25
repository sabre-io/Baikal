<?php

class Sabre_DAVACL_MockACLNode extends Sabre_DAV_Node implements Sabre_DAVACL_IACL {

    public $name;
    public $acl;

    function __construct($name, array $acl = array()) {

        $this->name = $name;
        $this->acl = $acl;

    }

    function getName() {

        return $this->name;

    }

    function getOwner() {

        return null;

    }

    function getGroup() {

        return null;

    }

    function getACL() {

        return $this->acl;

    }

    function setACL(array $acl) {

        $this->acl = $acl;

    }

}
