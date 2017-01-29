<?php

namespace Baikal\Service;

use Baikal\Version;

class ConfigService {

    /**
     * Returns the configuration, as it's stored in the configuration file.
     *
     * @return array
     */
    function get() {

        if ($this->exists()) {
            $config = include $this->getConfigFileName();
            $config['isDefault'] = false;
        } else {
            return $this->getDefault();
        }

    }

    /**
     * Does the configuration file exists?
     *
     * @return bool
     */
    function exists() {

        return file_exists($this->getConfigFileName());

    }

    /**
     * The default configuration. This will be used if baikal was not
     * installed yet.
     *
     * @return array
     */
    function getDefault() {

        return [
            'isDefault' => true,
            'version'   => Version::VERSION,
            'caldav'    => [
                'enabled' => true,
            ],
            'carddav' => [
                'enabled' => true,
            ],
            'auth' => [
                'type'  => 'Digest',
                'realm' => 'BaikalDAV',
            ],
            'debug' => true,
            'pdo'   => [
                'dsn'      => null,
                'username' => null,
                'password' => null,
            ],
            // Initially the password is 'admin'.
            'adminPassword' => '142ff212f9ed2f8f8b5e7b96f6929f78',
        ];

    }

    /**
     * Updates configuration, writing to the configuration file.
     *
     * @return array
     */
    function set(array $config) {

        $configStr = "<?php\n// This configuration file is automatically generated. Any changes made here may be overwritten.\n\nreturn " . var_export($config, true) . ';';

        // This is a generated configuration property. It doesn't need to be
        // stored.
        unset($config['isDefault']);

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

        return '/../../config/config.php';

    }

}
