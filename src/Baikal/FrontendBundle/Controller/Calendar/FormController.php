<?php

namespace Baikal\FrontendBundle\Controller\Calendar;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpKernel\Exception\HttpException;

use Baikal\SystemBundle\Entity\User,
    Baikal\ModelBundle\Entity\Calendar;

class FormController extends Controller {

    public function newAction(Request $request) {
        return $this->action($request);
    }

    public function editAction(Request $request, Calendar $calendar) {
        if(!$this->get('security.context')->isGranted('dav.read', $calendar)) {
            throw new HttpException(401, 'Unauthorized access.');
        }

        return $this->action($request, $calendar);
    }

    protected function action(Request $request, Calendar $calendar = null) {
        $that = $this;
        $user = $this->get('security.context')->getToken()->getUser();

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
            return $that->redirect($that->generateUrl('baikal_frontend_calendar_list'));
        };
    }

    protected function getFailureFunction(User $user) {
        $that = $this;
        
        return function($form, Calendar $calendar, $isNew) use ($user, $that) {
            return $this->render('BaikalFrontendBundle:Calendar:form.html.twig', array(
                'form' => $form->createView(),
                'user' => $user,
                'calendar' => $calendar
            ));
        };
    }
}
