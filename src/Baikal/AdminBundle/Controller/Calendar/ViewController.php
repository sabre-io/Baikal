<?php

namespace Baikal\AdminBundle\Controller\Calendar;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpKernel\Exception\HttpException;

use Baikal\SystemBundle\Entity\User,
    Baikal\ModelBundle\Entity\Calendar;

class ViewController extends Controller {

    public function indexAction(Request $request, User $user, Calendar $calendar) {

        if(!$this->get('security.context')->isGranted('dav.read', $calendar)) {
            throw new HttpException(401, 'Unauthorized access.');
        }

        return $this->render('BaikalAdminBundle:Calendar:view.html.twig', array(
            'user' => $user,
            'calendar' => $calendar,
        ));
    }

    public function fullscreenAction(Request $request, User $user, Calendar $calendar) {

        if(!$this->get('security.context')->isGranted('dav.read', $calendar)) {
            throw new HttpException(401, 'Unauthorized access.');
        }

        return $this->render('BaikalAdminBundle:Calendar:view.fullscreen.html.twig', array(
            'user' => $user,
            'calendar' => $calendar,
        ));
    }
}
