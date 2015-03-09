<?php

namespace Baikal\SystemBundle\Services\ConfigLoader;

abstract class AbstractConfigLoaderService {

    abstract public function load($filename);

    protected function prepareParameters(array $parameters) {
        reset($parameters);
        
        $keys = array_keys($parameters);
        foreach($keys as $key) {
            $parameters['%' . $key . '%'] = $parameters[$key];
        }

        reset($parameters);
        return $parameters;
    }

    protected function doReplacements($value) {
        
        if(!$this->parameters) {
            return $value;
        }

        if(is_array($value)) {
            $keys = array_keys($value);

            foreach($keys as $key) {
                $value[$key] = $this->doReplacements($value[$key]);
            }

            return $value;
        }

        if(is_string($value)) {
            return strtr($value, $this->parameters);
        }

        return $value;
    }
}