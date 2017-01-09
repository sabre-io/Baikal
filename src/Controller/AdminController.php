<?php

namespace Baikal\Controller;

use Silex\Api\ControllerProviderInterface;
use Silex\Application;

class AdminController implements ControllerProviderInterface {

    function connect(Application $app) {

        $controllers = $app['controllers_factory'];
        $controllers->get('/',          [$this, 'dashboardAction'])->bind('admin_dashboard');
        $controllers->mount('/users',   $app['controller.user']->connect($app));
        $controllers->mount('/settings', $app['controller.settings']->connect($app));

        return $controllers;
   }

    function dashboardAction(Application $app) {
        
        $statsService = $app['service.stats'];

        return $app['twig']->render('admin/dashboard.html', [
            'users'       => $statsService->users(),
            'nbcalendars' => $statsService->calendars(),
            'nbevents'    => $statsService->events(),
            'nbtasks'     => $statsService->tasks(),
            'nbbooks'     => $statsService->addressBooks(),
            'nbcontacts'  => $statsService->cards(),
        ]);
    }

}
