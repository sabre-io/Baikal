<?php

namespace Baikal;

use PDO;
use Silex\Provider\TwigServiceProvider;
use Symfony\Component\HttpFoundation\Request;

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
        $this->initRoutes();
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
            return new Controller\Admin\DashboardController($this['twig'], $this['url_generator'], $this['repository.user']);
        };

        $this['admin.user.controller'] = function() {
            return new Controller\Admin\UserController($this['twig'], $this['url_generator'], $this['repository.user']);
        };

        $this['admin.settings.standard.controller'] = function() {
            return new Controller\Admin\StandardSettingsController($this['twig'], $this['url_generator']);
        };

        $this['admin.settings.system.controller'] = function() {
            return new Controller\Admin\SystemSettingsController($this['twig'], $this['url_generator']);
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
            $pdo = new PDO(
                $this['config']['pdo']['dsn'],
                $this['config']['pdo']['username'],
                $this['config']['pdo']['password']
            );
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        };

        $this['repository.user'] = function() {
            return new Repository\UserRepository(
                $this['pdo'],
                $this['config']['auth']['realm']
            );
        };

    }

    /**
     * Initializes routes
     */
    protected function initRoutes() {

        $this->get('/', 'index.controller:indexAction')->bind('home');
        $this->get('/admin', 'admin.dashboard.controller:indexAction')->bind('admin_dashboard');

        $this->get('/admin/users', 'admin.user.controller:indexAction')->bind('admin_user_index');
        $this->get('/admin/users/new', 'admin.user.controller:createAction')->bind('admin_user_create');
        $this->post('/admin/users/new', 'admin.user.controller:postCreateAction')->bind('admin_user_create_post');
        $this->get('/admin/users/{userName}', 'admin.user.controller:editAction')->bind('admin_user_edit');
        $this->post('/admin/users/{userName}', 'admin.user.controller:postEditAction')->bind('admin_user_edit_post');

        $this->get('/admin/user/{userName}/addressbooks', 'admin.user.controller:addressbookAction')->bind('admin_user_addressbooks');
        $this->get('/admin/user/{userName}/calendars', 'admin.user.controller:calendarAction')->bind('admin_user_calendars');
        $this->get('/admin/user/{userName}/delete', 'admin.user.controller:deleteAction')->bind('admin_user_delete');
        $this->post('/admin/user/{userName}/delete', 'admin.user.controller:postDeleteAction')->bind('admin_user_delete_post');

        $this->get('/admin/settings/standard', 'admin.settings.standard.controller:indexAction')->bind('admin_settings_standard_index');

        $this->get('/admin/settings/system', 'admin.settings.system.controller:indexAction')->bind('admin_settings_system_index');

        $this->get('/admin/logout', 'admin.controller:logoutAction')->bind('admin_logout');

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
