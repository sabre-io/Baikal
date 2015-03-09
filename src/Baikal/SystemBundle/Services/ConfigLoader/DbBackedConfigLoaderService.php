<?php

namespace Baikal\SystemBundle\Services\ConfigLoader;

use Doctrine\ORM\EntityManager;

class DbBackedConfigLoaderService extends AbstractConfigLoaderService {

    protected $em;
    protected $configcontainerclass;
    protected $parameters;

    public function __construct(EntityManager $em, $configcontainerclass, $parameters = array()) {
        $this->em = $em;
        $this->configcontainerclass = $configcontainerclass;
    }

    public function load($configname) {
        
        $configEntity = $this->em->getRepository($this->configcontainerclass)->findOneByName($configname);
        
        if(!$configEntity) {
            return null;
        }

        return $configEntity;
    }
}