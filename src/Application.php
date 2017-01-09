<?php

namespace Baikal;

use PDO;
use Silex\Provider\SessionServiceProvider;
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

        $this['controller.admin'] = function() {
            return new Controller\AdminController();
        };

        $this['controller.index'] = function() {
            return new Controller\IndexController();
        };

        $this['controller.user'] = function() {
            return new Controller\UserController();
        };

        $this['controller.settings'] = function() {
            return new Controller\SettingsController();
        };

    }

    protected function initMiddleware() {

        $this->before(function(Request $request) {

            // Twig global variables
            $this['twig']->addGlobal('assetPath', dirname($request->getBaseUrl()) . '/assets/');
            $this['twig']->addGlobal('authenticated', $this['session']->get('authenticated'));

            switch ($request->getPathInfo()) {

                case '/login' :
                case '/logout' :
                case '/' :
                   return;
                default:
                    if ($this['session']->get('authenticated')) {
                        return;
                    }
                    return $this->redirect($this['url_generator']->generate('login'));

            }

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
        $this->register(new SessionServiceProvider());

        $this['pdo'] = function() {
            $pdo = new PDO(
                $this['config']['pdo']['dsn'],
                $this['config']['pdo']['username'],
                $this['config']['pdo']['password']
            );
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        };

        $this['service.stats'] = function() {

            return new Service\StatsService(
                $this['pdo']
            );

        };

        $this['service.config'] = function() {

            return new Service\ConfigService();

        };

        $this['service.user'] = function() {
            return new Service\UserService(
                $this['pdo'],
                $this['config']['auth']['realm'],
                $this['sabredav.backend.caldav'],
                $this['sabredav.backend.carddav']
            );
        };

        $this['service.calendar'] = function() {
            return new Service\CalendarService(
                $this['sabredav.backend.caldav']
            );
        };

        $this['service.addressbook'] = function() {
            return new Service\AddressbookService(
                $this['sabredav.backend.carddav']
            );
        };

    }

    /**
     * Initializes routes
     */
    protected function initRoutes() {

        $this->mount('/',       $this['controller.index']);
        $this->mount('/admin',  $this['controller.admin']);

        /*
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
         */

    }

    /**
     * Initializes all sabre/dav services
     */
    protected function initSabreDAV() {

        $this['sabredav.backend.caldav'] = function() {

            return new \Sabre\CalDAV\Backend\PDO($this['pdo']);

        };

        $this['sabredav.backend.carddav'] = function() {

            return new \Sabre\CardDAV\Backend\PDO($this['pdo']);

        };

        $this['sabredav'] = function() {

            return new DAV\Server($this);

        };

    }

}
