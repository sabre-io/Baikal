<?php

namespace Baikal\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpFoundation\RedirectResponse;

class WellKnownController extends Controller
{
    public function indexAction($service)
    {
        switch($service) {
            case 'caldav': {
                return new RedirectResponse($this->generateUrl("baikal_dav_services_caldav"), 301); # 302
                break;
            }
            case 'carddav': {
                return new RedirectResponse($this->generateUrl("baikal_dav_services_carddav"), 301); # 302
                break;
            }
        }

        return new Response('<h1>' . $service . ' is not well known !</h1>', 404);
    }
}
