<?php

namespace Baikal\SystemBundle\Services;

use Baikal\KernelBundle\Services\BaikalConfigServiceInterface;

class MainConfigService extends AbstractConfigService implements BaikalConfigServiceInterface {
    public function getServerTimezone() {
        return new \DateTimeZone($this->config->get('server_timezone'));
    }

    public function getEnable_api() {
        return TRUE;
    }
}