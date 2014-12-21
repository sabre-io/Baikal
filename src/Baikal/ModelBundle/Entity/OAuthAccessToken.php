<?php

namespace Baikal\ModelBundle\Entity;

use FOS\OAuthServerBundle\Entity\AccessToken as BaseAccessToken;
use Doctrine\ORM\Mapping as ORM;

class OAuthAccessToken extends BaseAccessToken {

    protected $client;

    protected $user;
}