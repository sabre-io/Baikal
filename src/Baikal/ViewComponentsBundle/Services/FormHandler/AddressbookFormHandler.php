<?php

namespace Baikal\ViewComponentsBundle\Services\FormHandler;

use Symfony\Component\HttpFoundation\Request;

use Sabre\DAV\UUIDUtil;

use Baikal\ModelBundle\Entity\User,
    Baikal\ModelBundle\Entity\Addressbook,
    Baikal\ModelBundle\Form\Type\Addressbook\AddressbookType;

class AddressbookFormHandler {
    
    protected $em;
    protected $formfactory;

    public function __construct($em, $formfactory) {
        $this->em = $em;
        $this->formfactory = $formfactory;
    }

    public function handle($onSuccess, $onFailure, Request $request, User $user, Addressbook $addressbook = null) {

        $new = false;

        if(is_null($addressbook)) {
            $addressbook = new Addressbook();
            $addressbook->setPrincipaluri($user->getIdentityPrincipal()->getUri());
            $addressbook->setUri(UUIDUtil::getUUID());
            $addressbook->setSynctoken('1');
            $new = true;
        }
        
        $form = $this->formfactory->create(
            new AddressbookType(),
            $addressbook
        );

        $form->handleRequest($request);

        if($form->isValid()) {
            $this->em->persist($addressbook);
            $this->em->flush();

            return $onSuccess($form, $addressbook, $new);
        }
        
        return $onFailure($form, $addressbook, $new);
    }
}