<?php

namespace Baikal\FrontendBundle\Controller\Addressbook;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpKernel\Exception\HttpException;

use Baikal\SystemBundle\Entity\User,
    Baikal\ModelBundle\Entity\Addressbook;

class ViewController extends Controller {

    public function indexAction(Request $request, Addressbook $addressbook) {
        
        if(!$this->get('security.context')->isGranted('dav.read', $addressbook)) {
            throw new HttpException(401, 'Unauthorized access.');
        }

        $user = $this->get('security.context')->getToken()->getUser();
        return $this->render('BaikalFrontendBundle:Addressbook:view.html.twig', array(
            'user' => $user,
            'addressbook' => $addressbook,
        ));
    }
}