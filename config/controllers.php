<?php

/** @var Silex\Application $app */
use Baikal\Framework\Silex\Controller\Admin\UserController;
use Baikal\Framework\Silex\Controller\AdminController;
use Baikal\Framework\Silex\Controller\Admin\DashboardController;
use Baikal\Framework\Silex\Controller\IndexController;

$app['index.controller'] = function($app) {
    return new IndexController($app['twig'], $app['url_generator']);
};

$app['admin.controller'] = function($app) {
    return new AdminController($app['twig'], $app['url_generator']);
};

$app['admin.dashboard.controller'] = function($app) {
    return new DashboardController($app['twig'], $app['url_generator'], $app['admin.user.repository']);
};

$app['admin.user.controller'] = function($app) {
    return new UserController($app['twig'], $app['url_generator'], $app['admin.user.repository']);
};
