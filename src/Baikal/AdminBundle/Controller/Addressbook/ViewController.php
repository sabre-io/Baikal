<?php

namespace Baikal\AdminBundle\Controller\Addressbook;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request;

use Baikal\SystemBundle\Entity\User,
    Baikal\ModelBundle\Entity\Addressbook;

class ViewController extends Controller {

    public function indexAction(Request $request, User $user, Addressbook $addressbook) {
        return $this->render('BaikalAdminBundle:Addressbook:view.html.twig', array(
            'user' => $user,
            'addressbook' => $addressbook,
        ));
    }
}
