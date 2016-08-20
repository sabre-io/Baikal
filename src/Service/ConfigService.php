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
     * Updates configuration, writing to the configuration file.
     *
     * @return array
     */
    function set(array $config) {

        $configStr = "<?php\n// This configuration file is automatically generated. Any changes made here may be overwritten.\n\nreturn " . var_export($config, true) . ';';

        file_put_contents(
            $this->getConfigFileName(),
            $configStr
        );

        if (function_exists('opcache_reset')) {
            opcache_reset();
        }

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
