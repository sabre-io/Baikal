<?php

namespace Baikal\AdminBundle\Controller\Application;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request;

use Baikal\ModelBundle\Entity\OAuthClient as Application;

class FormController extends Controller {

    public function newAction(Request $request) {
        return $this->action($request);
    }

    public function editAction(Request $request, Application $application) {
        return $this->action($request, $application);
    }

    protected function action(Request $request, Application $application = null) {
        $that = $this;

        return $this->get('baikal.formhandler.application')->handle(
            $this->getSuccessFunction(),
            $this->getViewFunction(),
            $request,
            $application   # if null, creation
        );
    }

    protected function getSuccessFunction() {
        $that = $this;

        return function($form, Application $application, $isNew) use($that) {
            $that->get('session')->getFlashBag()->add('notice', 'Application <i class="fa fa-cube"></i> <strong>' . htmlspecialchars($application->getName()) . '</strong> has been ' . ($isNew ? 'created' : 'updated') . '.');
            return $that->redirect($this->generateUrl('baikal_admin_application_list'));
        };
    }

    protected function getViewFunction() {
        $that = $this;
        
        return function($form, Application $application, $isNew) use ($that) {
            $nbusers = $this->get('baikal.repository.oauthaccesstoken')->countForClient($application);

            return $this->render('BaikalAdminBundle:Application:form.html.twig', array(
                'form' => $form->createView(),
                'application' => $application,
                'nbusers' => $nbusers
            ));
        };
    }
}