<?php

namespace Baikal\Controller;

use Silex\Api\ControllerProviderInterface;
use Silex\Application;

class AdminController implements ControllerProviderInterface {

    function connect(Application $app) {

        $controllers = $app['controllers_factory'];
        $controllers->get('/',          [$this, 'dashboard'])->bind('admin_dashboard');
        $controllers->get('/logout',    [$this, 'logout'])->bind('admin_logout');

        $controllers->mount('/users',   $app['controller.user']->connect($app));
        $controllers->mount('/settings', $app['controller.settings']->connect($app));

        return $controllers;
    }

    function dashboard(Application $app) {

        $statsService = $app['stats'];

        return $app['twig']->render('admin/dashboard.html', [
            'users'       => $statsService->users(),
            'nbcalendars' => $statsService->calendars(),
            'nbevents'    => $statsService->events(),
            'nbtasks'     => $statsService->tasks(),
            'nbbooks'     => $statsService->addressBooks(),
            'nbcontacts'  => $statsService->cards(),
        ]);


    }

    function logout() {
        return $this->redirect('admin_dashboard');
    }

}
