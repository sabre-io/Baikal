<?php

namespace Baikal\ModelBundle\Services;

use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

class SabreDavPasswordEncoder implements PasswordEncoderInterface {

    public function __construct($davrealm) {
        $this->davrealm = $davrealm;
    }

    public function encodePassword($raw, $salt) {
        return md5($salt . ':' . $this->davrealm . ':' . $raw); # $salt contains the username
    }

    public function isPasswordValid($encoded, $raw, $salt) {
        return $encoded === $this->encodePassword($raw, $salt);
    }
}