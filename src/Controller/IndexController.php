<?php

namespace Baikal\Controller;

use Silex\Api\ControllerProviderInterface;
use Silex\Application;

class IndexController implements ControllerProviderInterface {

    function connect(Application $app) {

        $controllers = $app['controllers_factory'];
        $controllers->get('/', [$this, 'indexAction'])->bind('home');

        return $controllers;
   }
   
    function indexAction(Application $app)
    {
        return $app['twig']->render('index.html');
    }
}
