<?php

namespace Baikal\SystemBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\Security\Core\Exception\AccessDeniedException;

class SecurityController extends Controller {

    public function accessDeniedAction(AccessDeniedException $exception) {
        return $this->render('BaikalSystemBundle:Security:accessDenied.html.twig');
    }
}
