<?php

namespace Baikal\Controller;

use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class SettingsController implements ControllerProviderInterface {

    function connect(Application $app) {

        $controllers = $app['controllers_factory'];
        $controllers->get('/', [$this, 'viewSettings'])->bind('admin_settings');
        $controllers->post('/', [$this, 'updateSettings'])->bind('admin_settings_post');
        return $controllers;
    }

    function viewSettings(Application $app) {

        return $app['twig']->render('admin/settings.html', [
            'config'      => $app['service.config']->get(),
            'is_writable' => $app['service.config']->isWritable()
        ]);
    }

    function updateSettings(Application $app, Request $request) {

        $configService = $app['service.config'];

        $oldConfig = $configService->get();
        $formData = $request->get('config');

        $newConfig = $oldConfig;

        $newConfig['caldav']['enabled'] = isset($formData['caldav']['enabled']);
        $newConfig['carddav']['enabled'] = isset($formData['carddav']['enabled']);
        $newConfig['debug'] = isset($formData['debug']);

        if (isset($formData['auth']['authType'])) {
            $newConfig['auth']['authType'] = $formData['auth']['authType'];
        }

        $configService->set($newConfig);

        return $app->redirect(
            $app['url_generator']->generate('admin_dashboard')
        );

    }

}
