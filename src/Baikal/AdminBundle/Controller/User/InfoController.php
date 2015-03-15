<?php

namespace Baikal\AdminBundle\Controller\User;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response;

use Baikal\SystemBundle\Entity\User;

class InfoController extends Controller
{
    public function indexAction(Request $request, User $user)
    {
        return $this->render('BaikalAdminBundle:User:info.html.twig', array(
            'nbcalendars' => count($user->getCalendars()),
            'nbbooks' => count($user->getAddressbooks()),
            'user' => $user,
        ));
    }
}
