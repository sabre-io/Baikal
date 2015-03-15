<?php

namespace Baikal\AdminBundle\Controller\Calendar;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Baikal\SystemBundle\Entity\User,
    Baikal\ModelBundle\Entity\Calendar;

class ListController extends Controller {

    public function indexAction(User $user) {
        
        $calendars = $this->get('baikal.repository.calendar')->findByUser($user);

        $that = $this;
        
        return $this->render('BaikalAdminBundle:Calendar:list.html.twig', array(
            'user' => $user,
            'calendars' => $calendars,
            'urls' => array(
                'view' => function($user, $calendar) use (&$that) {
                    return $that->generateUrl('baikal_admin_calendar_view', array('user' => $user->getId(), 'calendar' => $calendar->getId()));
                },
                'edit' => function($user, $calendar) use (&$that) {
                    return $that->generateUrl('baikal_admin_calendar_form_edit', array('user' => $user->getId(), 'calendar' => $calendar->getId()));
                },
                'delete' => function($user, $calendar) use (&$that) {
                    return $that->generateUrl('baikal_admin_calendar_list_delete', array('user' => $user->getId(), 'calendar' => $calendar->getId()));
                },
                'instructions' => function($user, $calendar) use (&$that) {
                    return $that->generateUrl('baikal_admin_calendar_instructions', array('user' => $user->getId(), 'calendar' => $calendar->getId()));
                },
            )
        ));
    }

    public function deleteAction(User $user, Calendar $calendar) {

        $em = $this->getDoctrine()->getManager();
        $em->remove($calendar);
        $em->flush();
        
        $this->get('session')->getFlashBag()->add('notice', 'Calendar <i class="fa fa-calendar"></i> <strong>' . htmlspecialchars($calendar->getDisplayname()) . '</strong> has been deleted.');
        return $this->redirect($this->generateUrl('baikal_admin_user_calendar_list', array('id' => $user->getId())));
    }
}
