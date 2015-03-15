<?php

namespace Baikal\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DashboardController extends Controller
{
    public function indexAction()
    {
        $nbusers = $this->getDoctrine()->getManager()->getRepository('\Baikal\SystemBundle\Entity\User')->countAll();

        $nbcalendars = $this->get('baikal.repository.calendar')->countAll();
        $nbevents = $this->get('baikal.repository.event')->countAll();

        $nbaddressbooks = $this->get('baikal.repository.addressbook')->countAll();
        $nbaddressbookcontacts = $this->getDoctrine()->getManager()->getRepository('\Baikal\ModelBundle\Entity\AddressbookContact')->countAll();

        return $this->render('BaikalAdminBundle:Dashboard:index.html.twig', array(
            'nbusers' => $nbusers,
            'nbcalendars' => $nbcalendars,
            'nbevents' => $nbevents,
            'nbaddressbooks' => $nbaddressbooks,
            'nbaddressbookcontacts' => $nbaddressbookcontacts,
        ));
    }
}
