<?php

namespace Baikal\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Response;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class AuthController extends Controller {

    public function loginformAction() {

        if($this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw new \Exception("Error Processing Request", 1);
            // redirect authenticated users to homepage
            return $this->redirect($this->generateUrl('baikal_admin_homepage'));
        }

        if($this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            throw new \Exception("Error Processing Request", 1);
            // redirect authenticated users to homepage
            return $this->redirect($this->generateUrl('baikal_admin_homepage'));
        }

        $request = $this->getRequest();
        $session = $request->getSession();

        // get the login error if there is one
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }

        return $this->render('BaikalAdminBundle:Auth:loginform.html.twig', array(
            // last username entered by the user
            'last_username' => $session->get(SecurityContext::LAST_USERNAME),
            'error' => $error,
        ));
    }
}
