<?php

namespace Baikal\Controller;

use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class IndexController implements ControllerProviderInterface {

    function connect(Application $app) {

        $controllers = $app['controllers_factory'];
        $controllers->get('/', [$this, 'indexAction'])->bind('home');
        $controllers->get('/login',     [$this, 'loginAction'])->bind('login');
        $controllers->post('/login',    [$this, 'postLoginAction']);
        $controllers->get('/logout',    [$this, 'logoutAction'])->bind('logout');

        return $controllers;
    }

    function indexAction(Application $app) {
        return $app['twig']->render('index.html');
    }

    function loginAction(Application $app) {

        if ($app['session']->getFlashBag()->has('error') || $app['session']->getFlashBag()->has('info')) {
            $msg = $app['session']->getFlashBag()->all();
            $app['twig']->addGlobal('msg', $msg);
        }

        return $app['twig']->render('login.html');
    }

    function postLoginAction(Application $app, Request $request) {

        $loginData = $request->get('data');
        $loginAdminPasswordHash = md5('admin:' . $app['config']['auth']['realm'] . ':' . $loginData['password']);

        if ($loginData['login'] === 'admin' && $loginAdminPasswordHash === $app['config']['admin_passwordhash']) {
            $app['session']->set('authenticated', true);
            return $app->redirect($app['url_generator']->generate('admin_dashboard'));
        }

        $app['session']->getFlashBag()->set('error', 'The password you provided is invalid. Please retry.');
        return $app->redirect($app['url_generator']->generate('login'));
    }

    function logoutAction(Application $app) {
        $app['session']->clear();
        $app['session']->getFlashBag()->set('info', 'You are logged out.');
        return $app->redirect($app['url_generator']->generate('login'));
    }

}
