<?php

namespace Baikal;

use Symfony\Component\HttpFoundation\Request;

class Application extends \Silex\Application {

    /**
     * Creates the Application instance.
     *
     * @param array $values
     */ 
    function __construct(array $values = []) {

        parent::__construct($values);
        $this->initControllers();
        $this->initMiddleware();

    }

    /**
     * Initialize Silex controllers
     */
    protected function initControllers() {

        $this['index.controller'] = function() {
            return new Controller\IndexController($this['twig'], $this['url_generator']);
        };

        $this['admin.controller'] = function() {
            return new Controller\AdminController($this['twig'], $this['url_generator']);
        };

        $this['admin.dashboard.controller'] = function() {
            return new Controller\Admin\DashboardController($this['twig'], $this['url_generator'], $this['admin.user.repository']);
        };

        $this['admin.user.controller'] = function() {
            return new Controller\Admin\UserController($this['twig'], $this['url_generator'], $this['admin.user.repository']);
        };

    }

    protected function initMiddleware() {

        $this->before(function(Request $request) {

            $this['twig']->addGlobal('assetPath', dirname($request->getBaseUrl()) . '/assets/');

        });

    }

}
