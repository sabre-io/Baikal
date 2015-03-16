<?php

namespace Baikal\ViewComponentsBundle\Twig;

use Symfony\Bundle\FrameworkBundle\Routing\Router,
    Symfony\Component\DependencyInjection\ContainerInterface;

use ICanBoogie\Inflector;

use Baikal\ModelBundle\Entity\Calendar;

class BaikalExtension extends \Twig_Extension {

    protected $container;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
        $this->inflector = Inflector::get();
    }
    
    public function getName() {
        return 'baikal';
    }

    public function getFunctions() {
        return array(
            'config' => new \Twig_SimpleFunction('config', array($this, 'config')),
            'systemstatus' => new \Twig_SimpleFunction('systemstatus', array($this, 'systemstatus')),
            'gravatarurl' => new \Twig_SimpleFunction('gravatarurl', array($this, 'gravatarurl'), array('is_safe' => array('html'))),
            'eventcountforcalendar' => new \Twig_SimpleFunction('eventcountforcalendar', array($this, 'eventcountforcalendar'), array('is_safe' => array('html'))),
            'pluralize' => new \Twig_SimpleFunction('pluralize', array($this, 'pluralize'), array('is_safe' => array('html'))),
            'singularize' => new \Twig_SimpleFunction('singularize', array($this, 'singularize'), array('is_safe' => array('html'))),
            'accord' => new \Twig_SimpleFunction('accord', array($this, 'accord'), array('is_safe' => array('html'))),
            'closure' => new \Twig_SimpleFunction('closure', array($this, 'closure'), array('is_safe' => array('html'))),
        );
    }

    # Returns the config Service, not the ConfigContainer entity
    public function config($configname = null) {

        if(!is_null($configname) && !preg_match('/^[a-z0-9\.]+$/i', $configname)) {
            throw new \Exception("Cannot access requested config in BootCamp Twig extension.");
        }

        if(is_null($configname)) {
            $configname = 'main';
        }

        return $this->container->get('config.' . $configname);
    }

    public function systemstatus() {

        $em = $this->container->get('doctrine.orm.entity_manager');
        $version = $em->getRepository('SymfonyBootCampBundle:BootCampStatus')->findAll();
        return $version[0];
    }

    public function gravatarurl($email, $width=200) {
        return '//www.gravatar.com/avatar/' . md5($email) . '?s=' . intval($width);
    }

    public function eventcountforcalendar(Calendar $calendar) {
        return $this->container->get('baikal.repository.event')->countAllByCalendar($calendar);
    }

    public function pluralize($string) {
        return $this->inflector->pluralize($string);
    }

    public function singularize($string) {
        return $this->inflector->singularize($string);
    }

    public function accord($string, $count) {
        if($count > 1) {
            return $this->pluralize($string);
        }

        return $this->singularize($string);
    }

    public function closure($closure) {
        return call_user_func_array($closure, array_slice(func_get_args(), 1));
    }
}