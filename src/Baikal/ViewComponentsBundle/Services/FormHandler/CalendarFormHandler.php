<?php

namespace Baikal\ViewComponentsBundle\Services\FormHandler;

use Symfony\Component\HttpFoundation\Request;

use Sabre\DAV\UUIDUtil;

use Baikal\ModelBundle\Entity\User,
    Baikal\ModelBundle\Entity\Calendar,
    Baikal\ModelBundle\Form\Type\Calendar\CalendarType;

class CalendarFormHandler {
    
    protected $em;
    protected $formfactory;

    public function __construct($em, $formfactory) {
        $this->em = $em;
        $this->formfactory = $formfactory;
    }

    public function handle($onSuccess, $onFailure, Request $request, User $user, Calendar $calendar = null) {

        $new = false;

        if(is_null($calendar)) {
            $calendar = new Calendar();
            $calendar->setPrincipaluri($user->getIdentityPrincipal()->getUri());
            $calendar->setUri(UUIDUtil::getUUID());
            $new = true;
        }
        
        $form = $this->formfactory->create(
            new CalendarType(),
            $calendar
        );

        $form->handleRequest($request);

        if($form->isValid()) {
            $this->em->persist($calendar);
            $this->em->flush();

            return $onSuccess($form, $calendar, $new);
        }
        
        return $onFailure($form, $calendar, $new);
    }
}