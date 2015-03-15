<?php

namespace Baikal\FrontendBundle\Controller\Calendar;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Baikal\SystemBundle\Entity\User;
use Baikal\ModelBundle\Entity\Calendar;

class ListController extends Controller {
    
    public function indexAction() {

        $that = $this;
        $user = $this->get('security.context')->getToken()->getUser();
        $calendars = $this->get('baikal.repository.calendar')->findByUser($user);
        
        return $this->render('BaikalFrontendBundle:Calendar:list.html.twig', array(
            'user' => $user,
            'calendars' => $calendars,
            'urls' => array(
                'view' => function($user, $calendar) use (&$that) {
                    return $that->generateUrl('baikal_frontend_calendar_view', array('calendar' => $calendar->getId()));
                },
                'edit' => function($user, $calendar) use (&$that) {
                    return $that->generateUrl('baikal_frontend_calendar_form_edit', array('calendar' => $calendar->getId()));
                },
                'delete' => function($user, $calendar) use (&$that) {
                    return $that->generateUrl('baikal_frontend_calendar_list_delete', array('calendar' => $calendar->getId()));
                },
                'instructions' => function($user, $calendar) use (&$that) {
                    return $that->generateUrl('baikal_frontend_calendar_instructions', array('calendar' => $calendar->getId()));
                },
            )
        ));
    }

    public function deleteAction(Calendar $calendar) {

        $em = $this->getDoctrine()->getManager();
        $em->remove($calendar);
        $em->flush();
        
        $this->get('session')->getFlashBag()->add('notice', 'Calendar <i class="fa fa-calendar"></i> <strong>' . htmlspecialchars($calendar->getDisplayname()) . '</strong> has been deleted.');
        return $this->redirect($this->generateUrl('baikal_frontend_calendar_list'));
    }
}
