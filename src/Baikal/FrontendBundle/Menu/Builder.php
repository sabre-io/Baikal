<?php

namespace Baikal\FrontendBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

class Builder extends ContainerAware {

    public function userMenu(FactoryInterface $factory, array $options) {

        $securityContext = $this->container->get('security.context');
        $user = $securityContext->getToken()->getUser();
        
        $menu = $factory->createItem('root', array('childrenAttributes' => array('class' => 'nav navbar-nav')));

        if($securityContext->isGranted('ROLE_FRONTEND_USER')) {
            $menu->addChild('Calendars', array('route' => 'baikal_frontend_calendar_list'));
            $menu->addChild('Contacts', array('route' => 'baikal_frontend_addressbook_list'));
            $menu->addChild('My profile', array('route' => 'baikal_frontend_profile'));
        }

        $currentpath = $this->container->get('request')->getPathInfo();

        foreach($menu as $item) {
            $item->setCurrent((bool)preg_match('%^' . preg_quote($item->getUri()) . '%', $currentpath));
        }

        return $menu;
    }
}