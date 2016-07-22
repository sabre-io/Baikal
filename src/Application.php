<?php

namespace Baikal;

use Symfony\Component\HttpFoundation\Request;
use Silex\Provider\TwigServiceProvider;

class Application extends \Silex\Application {

    /**
     * Creates the Application instance.
     *
     * @param array $values
     */
    function __construct(array $values = []) {

        parent::__construct($values);

        // Putting Silex in debug mode, if this was specified in the config.
        $this['debug'] = $this['config']['debug'];

        $this->initControllers();
        $this->initServices();
        $this->initMiddleware();
        $this->initSabreDAV();

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

    /**
     * Initializes silex services
     */
    protected function initServices() {

        // Twig
        $this->register(new TwigServiceProvider(), [
            'twig.path' => __DIR__ . '/../views/',
        ]);

        $this['resolver'] = function() {
            return new ControllerResolver($this);
        };

        $this['pdo'] = function() {
            return new \PDO($this['config']['pdo']['dsn'], $this['config']['pdo']['username'], $this['config']['pdo']['password']);
        };

        $this['admin.user.repository'] = function() {
            return new Infrastructure\Repository\PdoUserRepository($this['pdo']);
        };

    }

    /**
     * Initializes all sabre/dav services
     */
    protected function initSabreDAV() {

        $this['sabredav'] = function() {

            return new DAV\Server(
                $this['config']['caldav']['enabled'],
                $this['config']['carddav']['enabled'],
                $this['config']['auth']['type'],
                $this['config']['auth']['realm'],
                $this['pdo'],
                null
            );

        };

    }

}
