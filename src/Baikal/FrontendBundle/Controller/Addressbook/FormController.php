<?php

namespace Baikal\FrontendBundle\Controller\Addressbook;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpKernel\Exception\HttpException;

use Baikal\ModelBundle\Entity\User,
    Baikal\ModelBundle\Entity\Addressbook;

class FormController extends Controller {

    public function newAction(Request $request) {
        return $this->action($request);
    }

    public function editAction(Request $request, Addressbook $addressbook) {
        if(!$this->get('security.context')->isGranted('dav.read', $addressbook)) {
            throw new HttpException(401, 'Unauthorized access.');
        }

        return $this->action($request, $addressbook);
    }

    protected function action(Request $request, Addressbook $addressbook = null) {
        $that = $this;
        $user = $this->get('security.context')->getToken()->getUser();

        return $this->get('baikal.formhandler.addressbook')->handle(
            $this->getSuccessFunction($user),
            $this->getFailureFunction($user),
            $request,
            $user,
            $addressbook   # if null, creation
        );
    }

    protected function getSuccessFunction(User $user) {
        $that = $this;

        return function($form, Addressbook $addressbook, $isNew) use($user, $that) {
            $that->get('session')->getFlashBag()->add('notice', 'Addressbook <i class="fa fa-book"></i> <strong>' . htmlspecialchars($addressbook->getDisplayname()) . '</strong> has been ' . ($isNew ? 'created' : 'updated') . '.');
            return $that->redirect($that->generateUrl('baikal_frontend_addressbook_list'));
        };
    }

    protected function getFailureFunction(User $user) {
        $that = $this;
        
        return function($form, Addressbook $addressbook, $isNew) use ($user, $that) {
            return $this->render('BaikalFrontendBundle:Addressbook:form.html.twig', array(
                'form' => $form->createView(),
                'user' => $user,
                'addressbook' => $addressbook
            ));
        };
    }
}
