<?php

namespace Baikal\SystemBundle\Services;

use Doctrine\ORM\EntityManager;

abstract class AbstractConfigService {

    protected $entityManager;
    protected $config;

    public function __construct(EntityManager $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function initialize($config) {
        $this->config = $config;
    }

    public function __call($name, $arguments) {

        if(preg_match('/^get.+$/', $name)) {
            $prop = lcfirst(substr($name, 3));
            return $this->config->get($prop);
        }

        if(preg_match('/^set.+$/', $name)) {
            $prop = lcfirst(substr($name, 3));
            $this->config->set($prop, $arguments[0]);
            $this->entityManager->persist($this->config);
            $this->entityManager->flush();
            return $this;
        }

        if(preg_match('/^has.+$/', $name)) {
            $prop = lcfirst(substr($name, 3));
            return $this->config->has($prop);
        }

        throw new \RuntimeException(get_class($this) . ': Call to undefined method ' . $name);
    }
}