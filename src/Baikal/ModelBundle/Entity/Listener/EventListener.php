<?php

namespace Baikal\ModelBundle\Entity\Listener;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Doctrine\ORM\Event\LifecycleEventArgs;

use Sabre\VObject;

use Baikal\ModelBundle\Entity\Event as CalendarEvent;

class EventListener {

    public function __construct() {
    }

    public function preUpdate(CalendarEvent $calendarevent, LifecycleEventArgs $event) {
        #$calendarevent->setLastmodified(time());
        #$calendarevent->setEtag(md5($calendarevent->getCalendardata()));
    }
}