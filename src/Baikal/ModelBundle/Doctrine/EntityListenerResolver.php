<?php

namespace Baikal\ModelBundle\Doctrine;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\Mapping\DefaultEntityListenerResolver;

class EntityListenerResolver extends DefaultEntityListenerResolver
{
    private $container;
    private $mapping;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->mapping = array();
    }

    public function addMapping($className, $service)
    {
        $this->mapping[$className] = $service;
    }

    public function resolve($className)
    {
        if (isset($this->mapping[$className]) && $this->container->has($this->mapping[$className])) {
            return $this->container->get($this->mapping[$className]);
        }

        return parent::resolve($className);
    }
}