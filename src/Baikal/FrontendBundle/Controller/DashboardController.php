<?php

namespace Baikal\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DashboardController extends Controller
{
    public function indexAction()
    {
        return $this->render('BaikalFrontendBundle:Dashboard:index.html.twig', array(
            'user' => $this->get('security.context')->getToken()->getUser(),
        ));
    }
}
