<?php

namespace Baikal\Service;

class ConfigService {

    /**
     * Returns the configuration, as it's stored in the configuration file.
     *
     * @return array
     */
    function get() {

        return include $this->getConfigFileName();

    }

    /**
     * Returns true or false depending on if we are able to write to the
     * configuration file.
     *
     * @return bool
     */
    function isWritable() {

        return is_writable($this->getConfigFileName());

    }

    /**
     * Returns the full path to the configuration file.
     */
    protected function getConfigFileName() {

        return realpath(__DIR__ . '/../../config/config.php');

    }

}
