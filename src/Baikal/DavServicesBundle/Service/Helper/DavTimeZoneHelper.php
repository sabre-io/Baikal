<?php

namespace Baikal\DavServicesBundle\Service\Helper;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Sabre\VObject;

use Baikal\CoreBundle\Services\MainConfigService;

class DavTimeZoneHelper {

    protected $mainconfig;

    public function __construct(MainConfigService $mainconfig) {
        $this->mainconfig = $mainconfig;
    }
    
    public function extractTimeZoneFromDavString($davstring) {
        try {
            $timezone = VObject\TimeZoneUtil::getTimeZone(
                null,
                VObject\Reader::read($davstring),
                TRUE    # failIfUncertain
            );
        } catch(\Exception $e) {
            # Defaulting to Server timezone
            $timezone = $this->mainconfig->getServerTimezone();
        }

        return $timezone;
    }
}