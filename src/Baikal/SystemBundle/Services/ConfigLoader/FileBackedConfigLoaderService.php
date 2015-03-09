<?php

namespace Baikal\SystemBundle\Services\ConfigLoader;

use Symfony\Component\Yaml\Yaml;

class FileBackedConfigLoaderService extends AbstractConfigLoaderService {

    protected $parameters;

    public function __construct(array $parameters=array()) {
        $this->parameters = $this->prepareParameters($parameters);
    }

    public function load($filename) {
        $config = Yaml::parse($filename) ?: array();

        $keys = array_keys($config);
        foreach($keys as $key) {
            $config[$key] = $this->doReplacements($config[$key]);
        }

        return $config;
    }
}