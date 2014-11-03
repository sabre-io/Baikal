<?php

namespace Baikal\ViewComponentsBundle\Twig;

use Knp\Menu\Twig\MenuExtension as TwigMenuExtension;
use Knp\Menu\ItemInterface;
use Knp\Menu\Renderer\RendererProviderInterface;
use Knp\Menu\Provider\MenuProviderInterface;

class MenuExtension extends TwigMenuExtension
{
    public function getFunctions()
    {
        return array(
            'navbarmenu' => new \Twig_Function_Method($this, 'navbarmenu', array('is_safe' => array('html'))),
        );
    }

    public function navbarmenu($menu, array $options = array(), $renderer = null)
    {
        $options['class'] = 'navbar-nav';
        $options['currentClass'] = 'active';

        $options['template'] = 'BaikalViewComponentsBundle:Menu:topmenu.html.twig';
        return parent::render($menu, $options, $renderer);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'baikal_menu';
    }
}