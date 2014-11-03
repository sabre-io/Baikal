<?php

namespace Baikal\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class SecurityController extends Controller {

    public function accessDeniedAction(AccessDeniedException $exception) {
        return $this->render('BaikalCoreBundle:Security:accessDenied.html.twig');
    }
}
