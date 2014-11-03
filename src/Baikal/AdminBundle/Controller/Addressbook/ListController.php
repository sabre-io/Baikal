<?php

namespace Baikal\AdminBundle\Controller\Addressbook;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Baikal\ModelBundle\Entity;

class ListController extends Controller {

    public function indexAction(Entity\User $user) {
        
        $addressbooks = $this->get('baikal.repository.addressbook')->findByUser($user);

        $that = $this;
        
        return $this->render('BaikalAdminBundle:Addressbook:list.html.twig', array(
            'user' => $user,
            'addressbooks' => $addressbooks,
            'urls' => array(
                'view' => function($user, $addressbook) use (&$that) {
                    return $that->generateUrl('baikal_admin_addressbook_view', array('user' => $user->getId(), 'addressbook' => $addressbook->getId()));
                },
                'edit' => function($user, $addressbook) use (&$that) {
                    return $that->generateUrl('baikal_admin_addressbook_form_edit', array('user' => $user->getId(), 'addressbook' => $addressbook->getId()));
                },
                'delete' => function($user, $addressbook) use (&$that) {
                    return $that->generateUrl('baikal_admin_addressbook_list_delete', array('user' => $user->getId(), 'addressbook' => $addressbook->getId()));
                }
            ),
        ));
    }

    public function deleteAction(Entity\User $user, Entity\Addressbook $addressbook) {

        $em = $this->getDoctrine()->getManager();
        $em->remove($addressbook);
        $em->flush();
        
        $this->get('session')->getFlashBag()->add('notice', 'Addressbook <i class="fa fa-book"></i> <strong>' . htmlspecialchars($addressbook->getDisplayname()) . '</strong> has been deleted.');
        return $this->redirect($this->generateUrl('baikal_admin_user_addressbook_list', array('id' => $user->getId())));
    }
}
