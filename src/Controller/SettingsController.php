<?php

namespace Baikal\Controller;

use Baikal\Controller\Controller;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig_Environment;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;

class SettingsController implements ControllerProviderInterface {

    function connect(Application $app) {

        $controllers = $app['controllers_factory'];
        $controllers->get('/', [$this,'indexAction'])->bind('admin_settings');
        return $controllers;
    }

    function indexAction(Application $app) {
        return $app['twig']->render('admin/settings.html');
    }
}
