<?php

namespace Baikal\FrontendBundle\Controller\Addressbook;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Baikal\ModelBundle\Entity;

class ListController extends Controller {

    public function indexAction() {
        
        $user = $this->get('security.context')->getToken()->getUser();
        $books = $this->get('baikal.repository.addressbook')->findByUser($user);

        $that = $this;
        
        return $this->render('BaikalFrontendBundle:Addressbook:list.html.twig', array(
            'user' => $user,
            'addressbooks' => $books,
            'urls' => array(
                'view' => function($user, $addressbook) use (&$that) {
                    return $that->generateUrl('baikal_frontend_addressbook_view', array('addressbook' => $addressbook->getId()));
                },
                'edit' => function($user, $addressbook) use (&$that) {
                    return $that->generateUrl('baikal_frontend_addressbook_form_edit', array('addressbook' => $addressbook->getId()));
                },
                'delete' => function($user, $addressbook) use (&$that) {
                    return $that->generateUrl('baikal_frontend_addressbook_list_delete', array('addressbook' => $addressbook->getId()));
                }
            ),
        ));
    }

    public function deleteAction(Entity\Addressbook $addressbook) {

        $em = $this->getDoctrine()->getManager();
        $em->remove($addressbook);
        $em->flush();
        
        $this->get('session')->getFlashBag()->add('notice', 'Addressbook <i class="fa fa-book"></i> <strong>' . htmlspecialchars($addressbook->getDisplayname()) . '</strong> has been deleted.');
        return $this->redirect($this->generateUrl('baikal_frontend_addressbook_list'));
    }
}
