<?php

namespace Baikal\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\Security\Core\SecurityContext,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response;


class AuthController extends Controller {

    public function loginformAction(Request $request) {
        return $this->authenticate(
            $request,
            'BaikalFrontendBundle:Auth:loginform.html.twig'
        );
    }

    public function oauthloginformAction(Request $request) {
        return $this->authenticate(
            $request,
            'BaikalFrontendBundle:Auth:oauthloginform.html.twig'
        );
    }

    protected function authenticate(Request $request, $templatepath) {
        
        if($this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw new \Exception("Error Processing Request", 1);
        }

        if($this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            throw new \Exception("Error Processing Request", 1);
        }

        $session = $request->getSession();

        # get the login error if there is one
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }

        return $this->render($templatepath, array(
            'last_username' => $session->get(SecurityContext::LAST_USERNAME),   # last username entered by the user
            'error' => $error,
        ));
    }
}
