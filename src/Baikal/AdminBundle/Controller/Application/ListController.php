<?php

namespace Baikal\AdminBundle\Controller\Application;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response;

use Baikal\ModelBundle\Entity\OAuthClient as Application;

class ListController extends Controller {

    public function indexAction(Request $request) {

        $that = $this;

        $apps = $this->get('baikal.repository.oauthclient')->findAll();
        return $this->render('BaikalAdminBundle:Application:list.html.twig', array(
            'apps' => $apps,
            'urls' => array(
                'edit' => function(Application $application) use (&$that) {
                    return $that->generateUrl('baikal_admin_application_form_edit', array('application' => $application->getId()));
                },
                'delete' => function(Application $application) use (&$that) {
                    return $that->generateUrl('baikal_admin_application_list_delete', array('application' => $application->getId()));
                }
            ),
        ));
    }

    public function deleteAction(Application $application) {

        $em = $this->getDoctrine()->getManager();
        $em->remove($application);
        $em->flush();
        
        $this->get('session')->getFlashBag()->add('notice', 'Application <i class="fa fa-cube"></i> <strong>' . htmlspecialchars($application->getName()) . '</strong> has been deleted.');
        return $this->redirect($this->generateUrl('baikal_admin_application_list'));
    }
}
