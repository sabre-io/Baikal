<?php

namespace Baikal\AdminBundle\Controller\User;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Baikal\SystemBundle\Entity\User;

class ListController extends Controller
{
    public function indexAction()
    {
        $users = $this->getDoctrine()->getManager()->getRepository('\Baikal\SystemBundle\Entity\User')->findAll();
        
        return $this->render('BaikalAdminBundle:User:list.html.twig', array(
            'users' => $users,
        ));
    }

    public function deleteAction(User $user)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();
        
        $this->get('session')->getFlashBag()->add('notice', 'User <i class="fa fa-user"></i> <strong>' . htmlspecialchars($user->getUsername()) . '</strong> has been deleted.');
        return $this->redirect($this->generateUrl('baikal_admin_user_list'));
    }
}
