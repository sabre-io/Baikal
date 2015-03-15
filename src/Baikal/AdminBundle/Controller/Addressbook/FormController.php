<?php

namespace Baikal\AdminBundle\Controller\Addressbook;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpKernel\Exception\HttpException;

use Baikal\SystemBundle\Entity\User,
    Baikal\ModelBundle\Entity\Addressbook;

class FormController extends Controller {

    public function newAction(Request $request, User $user) {
        return $this->action($request, $user);
    }

    public function editAction(Request $request, User $user, Addressbook $addressbook) {
        if(!$this->get('security.context')->isGranted('dav.read', $addressbook)) {
            throw new HttpException(401, 'Unauthorized access.');
        }

        return $this->action($request, $addressbook, $user);
    }

    protected function action(Request $request, User $user, Addressbook $addressbook = null) {
        $that = $this;

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
            return $that->redirect($this->generateUrl('baikal_admin_user_addressbook_list', array('id' => $user->getId())));
        };
    }

    protected function getFailureFunction(User $user) {
        $that = $this;
        
        return function($form, Addressbook $addressbook, $isNew) use ($user, $that) {
            return $this->render('BaikalAdminBundle:Addressbook:form.html.twig', array(
                'form' => $form->createView(),
                'user' => $user,
                'addressbook' => $addressbook
            ));
        };
    }
}