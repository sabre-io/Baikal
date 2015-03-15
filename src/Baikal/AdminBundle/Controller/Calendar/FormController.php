<?php

namespace Baikal\AdminBundle\Controller\Calendar;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpKernel\Exception\HttpException;

use Baikal\SystemBundle\Entity\User,
    Baikal\ModelBundle\Entity\Calendar,
    Baikal\FrontendBundle\Controller\Calendar\FormController as FrontendForm;

class FormController extends Controller {

    public function newAction(Request $request, User $user) {
        return $this->action($request, $user);
    }

    public function editAction(Request $request, User $user, Calendar $calendar) {
        if(!$this->get('security.context')->isGranted('dav.read', $calendar)) {
            throw new HttpException(401, 'Unauthorized access.');
        }

        return $this->action($request, $user, $calendar);
    }

    protected function action(Request $request, User $user, Calendar $calendar = null) {
        $that = $this;

        return $this->get('baikal.formhandler.calendar')->handle(
            $this->getSuccessFunction($user),
            $this->getFailureFunction($user),
            $request,
            $user,
            $calendar   # if null, creation
        );
    }

    protected function getSuccessFunction(User $user) {
        $that = $this;

        return function($form, Calendar $calendar, $isNew) use($user, $that) {
            $that->get('session')->getFlashBag()->add('notice', 'Calendar <i class="fa fa-calendar"></i> <strong>' . htmlspecialchars($calendar->getDisplayname()) . '</strong> has been ' . ($isNew ? 'created' : 'updated') . '.');
            return $that->redirect($this->generateUrl('baikal_admin_user_calendar_list', array('id' => $user->getId())));
        };
    }

    protected function getFailureFunction(User $user) {
        $that = $this;
        
        return function($form, Calendar $calendar, $isNew) use ($user, $that) {
            return $this->render('BaikalAdminBundle:Calendar:form.html.twig', array(
                'form' => $form->createView(),
                'user' => $user,
                'calendar' => $calendar
            ));
        };
    }
}