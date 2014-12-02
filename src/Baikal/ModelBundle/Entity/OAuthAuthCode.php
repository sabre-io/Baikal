<?php

namespace Baikal\ModelBundle\Entity;

use FOS\OAuthServerBundle\Entity\AuthCode as BaseAuthCode;
use Doctrine\ORM\Mapping as ORM;

class OAuthAuthCode extends BaseAuthCode {

    protected $client;

    protected $user;
}