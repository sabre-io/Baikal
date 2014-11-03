<?php

namespace Baikal\AdminBundle\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\Matcher\Matcher;
use Knp\Menu\Matcher\Voter\RegexVoter;

use Symfony\Component\DependencyInjection\ContainerAware;

class Builder extends ContainerAware {

    public function adminMenu(FactoryInterface $factory, array $options) {

        $menu = $factory->createItem('root', array('childrenAttributes' => array('class' => 'nav navbar-nav')));

        $menu->addChild('Users+Data', array('route' => 'baikal_admin_user_list'));
        $menu->addChild('Settings', array('route' => 'baikal_admin_settings'));

        $currentpath = $this->container->get('request')->getPathInfo();

        foreach($menu as $item) {
            $item->setCurrent((bool)preg_match('%^' . preg_quote($item->getUri()) . '%', $currentpath));
        }

        return $menu;
    }
}