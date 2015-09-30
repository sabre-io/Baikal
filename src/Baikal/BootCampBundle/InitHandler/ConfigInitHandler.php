<?php

namespace Baikal\BootCampBundle\InitHandler;

use Doctrine\ORM\EntityManager;

use Netgusto\BootCampBundle\InitHandler\ConfigInitHandlerInterface;

use Baikal\ModelBundle\Entity\ConfigContainer;

class ConfigInitHandler implements ConfigInitHandlerInterface {

    protected $entityManager;
    protected $passwordencoder_factory;

    public function __construct(EntityManager $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function createAndPersistConfig() {

        $siteconfig = new ConfigContainer();
        $siteconfig->setName('main');
        $siteconfig->setConfig(array(
            'server_timezone' => 'Europe/Paris',
            'enable_caldav' => true,
            'enable_carddav' => true,
            'enable_versioncheck' => true,
        ));

        $this->entityManager->persist($siteconfig);
        $this->entityManager->flush();
    }
}