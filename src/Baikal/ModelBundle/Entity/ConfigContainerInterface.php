<?php

namespace Baikal\ModelBundle\Entity;

interface ConfigContainerInterface {

    public function setName($name);
    public function getName();

    public function setConfig(array $config);
    public function getConfig();

    public function has($prop);
    public function get($prop);
    public function set($prop, $value);
}