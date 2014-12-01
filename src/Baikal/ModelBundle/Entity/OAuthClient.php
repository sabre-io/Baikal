<?php

namespace Baikal\ModelBundle\Entity;

use FOS\OAuthServerBundle\Entity\Client as BaseClient;

class OAuthClient extends BaseClient {

    protected $name;
    protected $description;
    protected $homepageurl;

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    public function getDescription() {
        return $this->description;
    }

    public function setDescription($description) {
        $this->description = $description;
        return $this;
    }

    public function getHomepageurl() {
        return $this->homepageurl;
    }

    public function setHomepageurl($homepageurl) {
        $this->homepageurl = $homepageurl;
        return $this;
    }

    public function getRedirecturi() {
        $uris = $this->getRedirecturis();
        if(count($uris) === 0) {
            return null;
        }
        
        return $uris[0];
    }

    public function setRedirecturi($redirecturi) {
        $this->setRedirecturis(array($redirecturi));
        return $this;
    }
}