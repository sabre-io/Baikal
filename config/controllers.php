<?php

/** @var Silex\Application $app */

use Baikal\Framework\Silex\Controller\DashboardController;
use Baikal\Framework\Silex\Controller\IndexController;

$app['index.controller'] = function($app) {
    $timelineController = new IndexController();
    $timelineController->setTemplate($app['twig']);
    return $timelineController;
};

$app['dashboard.controller'] = function($app) {
    $dashboardController = new DashboardController();
    $dashboardController->setTemplate($app['twig']);
    return $dashboardController;
};
