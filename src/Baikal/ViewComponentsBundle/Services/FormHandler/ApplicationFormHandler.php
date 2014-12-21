<?php

namespace Baikal\ViewComponentsBundle\Services\FormHandler;

use Symfony\Component\HttpFoundation\Request;

use Sabre\DAV\UUIDUtil;

use Baikal\ModelBundle\Entity\OAuthClient as Application,
    Baikal\ModelBundle\Form\Type\OAuth\ClientType as ApplicationType;

class ApplicationFormHandler {
    
    protected $em;
    protected $formfactory;

    public function __construct($em, $formfactory) {
        $this->em = $em;
        $this->formfactory = $formfactory;
    }

    public function handle($onSuccess, $onFailure, Request $request, Application $app = null) {

        $new = false;

        if(is_null($app)) {
            $app = new Application();
            $new = true;
        }
        
        $form = $this->formfactory->create(
            new ApplicationType(),
            $app
        );

        $form->handleRequest($request);

        if($form->isValid()) {
            $this->em->persist($app);
            $this->em->flush();

            return $onSuccess($form, $app, $new);
        }
        
        return $onFailure($form, $app, $new);
    }
}