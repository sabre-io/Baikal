<?php

/** @var Silex\Application $app */
use Baikal\Framework\Silex\Controller\AdminController;
use Baikal\Framework\Silex\Controller\DashboardController;
use Baikal\Framework\Silex\Controller\IndexController;

$app['index.controller'] = function($app) {
    $indexController = new IndexController();
    $indexController->setTemplate($app['twig']);
    return $indexController;
};

$app['admin.controller'] = function($app) {
    $adminController = new AdminController();
    $adminController->setTemplate($app['twig']);
    return $adminController;
};

$app['dashboard.controller'] = function($app) {
    $dashboardController = new DashboardController();
    $dashboardController->setTemplate($app['twig']);
    return $dashboardController;
};
