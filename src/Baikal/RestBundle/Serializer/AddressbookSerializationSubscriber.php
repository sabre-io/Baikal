<?php

namespace Baikal\RestBundle\Serializer;

use JMS\Serializer\EventDispatcher\EventSubscriberInterface,
    JMS\Serializer\EventDispatcher\ObjectEvent,
    JMS\Serializer\EventDispatcher\Events as SerializerEvents;

use Sabre\VObject;

use Baikal\CoreBundle\Services\MainConfigService;

class AddressbookSerializationSubscriber implements EventSubscriberInterface {
    
    protected $mainconfig;

    public function __construct(MainConfigService $mainconfig) {
        $this->mainconfig = $mainconfig;
    }

    public static function getSubscribedEvents() {
        return array(
            array(
                'event' => SerializerEvents::POST_SERIALIZE,
                'class' => 'Baikal\ModelBundle\Entity\Addressbook',
                'method' => 'onPostSerialize_Addressbook'
            ),
        );
    }

    public function onPostSerialize_Addressbook(ObjectEvent $event) {
        /*
        $addressbook = $event->getObject();

        $event->getVisitor()->addData(
            'links',
            array(
                'contacts' => '/addressbooks/' . $addressbook->getId() . '/contacts'
            )
        );
        */
    }
}