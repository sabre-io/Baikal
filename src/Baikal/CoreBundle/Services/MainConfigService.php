<?php

namespace Baikal\CoreBundle\Services;

class MainConfigService extends AbstractConfigService {
    public function getServerTimezone() {
        return new \DateTimeZone($this->config->get('server_timezone'));
    }

    public function getEnable_api() {
        return TRUE;
    }
}