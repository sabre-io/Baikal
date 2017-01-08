<?php

namespace Baikal\Controller;

use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class AdminController implements ControllerProviderInterface {

    function connect(Application $app) {

        $controllers = $app['controllers_factory'];
        $controllers->get('/',          [$this, 'dashboardAction'])->bind('admin_dashboard');
        $controllers->get('/login',     [$this, 'loginAction'])->bind('admin_login');
        $controllers->post('/login',    [$this, 'postLoginAction'])->bind('admin_login_post');
        $controllers->get('/logout',    [$this, 'logoutAction'])->bind('admin_logout');

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

    function loginAction(Application $app) {

        if ($app['session']->getFlashBag()->has('error') || $app['session']->getFlashBag()->has('info')) {
            $msg = $app['session']->getFlashBag()->all();
            $app['twig']->addGlobal('msg', $msg);
        }

        return $app['twig']->render('admin/login.html');
    }

    function postLoginAction(Application $app, Request $request) {
        
        if ($request->getMethod() !== Request::METHOD_POST) {
            throw new MethodNotAllowedException([Request::METHOD_POST]);
        }

        $loginData = $request->get('data');
        $loginAdminPasswordHash = md5('admin:' . $app['config']['auth_realm'] . ':' . $loginData['password']);

        if ($loginData['login'] === 'admin' && $loginAdminPasswordHash === $app['config']['admin_passwordhash']) {
            $app['session']->set('authenticated', true);
            return $app->redirect($app['url_generator']->generate('admin_dashboard'));
        }

        $app['session']->getFlashBag()->set('error', 'The password you provided is invalid. Please retry.');
        return $app->redirect($app['url_generator']->generate('admin_login'));
    }

    function logoutAction(Application $app) {
        $app['session']->clear();
        $app['session']->getFlashBag()->set('info', 'You are logged out.');
        return $app->redirect($app['url_generator']->generate('admin_login'));
    }

}
