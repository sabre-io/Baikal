<?php

namespace Baikal\Controller;

use Silex\Api\ControllerProviderInterface;
use Silex\Application;

class InstallController implements ControllerProviderInterface {

    protected $app;
        
    function connect(Application $app) {

        $controllers = $app['controllers_factory'];
        $controllers->get('', [$this, 'installAction'])->bind('install');
        $this->app = $app;

        return $controllers;

    }

    function installAction() {

        return 'hello darkness, my old friend';

    }

}
