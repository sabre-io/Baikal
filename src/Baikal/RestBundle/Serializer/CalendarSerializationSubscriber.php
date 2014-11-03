<?php

namespace Baikal\RestBundle\Serializer;

use JMS\Serializer\EventDispatcher\EventSubscriberInterface,
    JMS\Serializer\EventDispatcher\ObjectEvent;

use Sabre\VObject;

use Baikal\CoreBundle\Services\MainConfigService;

class CalendarSerializationSubscriber implements EventSubscriberInterface {
    
    protected $mainconfig;

    public function __construct(
        MainConfigService $mainconfig
    ) {
        $this->mainconfig = $mainconfig;
    }

    public static function getSubscribedEvents() {
        return array(
            array(
                'event' => 'serializer.post_serialize', 
                'class' => 'Baikal\ModelBundle\Entity\Calendar',
                'method' => 'onPostSerialize_Calendar'
            )
        );
    }

    public function onPostSerialize_Calendar(ObjectEvent $event) {
        
        $calendar = $event->getObject();

        try {
            $timezone = VObject\TimeZoneUtil::getTimeZone(
                null,
                VObject\Reader::read($calendar->getTimezone()),
                TRUE    # failIfUncertain
            );
        } catch(\Exception $e) {
            # Defaulting to Server timezone
            $timezone = $this->mainconfig->getServerTimezone();
        }

        $event->getVisitor()->addData(
            'timezone',
            $timezone->getName()
        );
    }
}