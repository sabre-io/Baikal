<?php

namespace Baikal\FrontendBundle\Controller\Calendar;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpKernel\Exception\HttpException;

use Baikal\SystemBundle\Entity\User,
    Baikal\ModelBundle\Entity\Calendar;

class InstructionsController extends Controller {

    public function indexAction(Request $request, Calendar $calendar) {

        if(!$this->get('security.context')->isGranted('dav.read', $calendar)) {
            throw new HttpException(401, 'Unauthorized access.');
        }

        $user = $this->get('security.context')->getToken()->getUser();
        
        return $this->render('BaikalFrontendBundle:Calendar:instructions.html.twig', array(
            'user' => $user,
            'calendar' => $calendar,
        ));
    }
}
