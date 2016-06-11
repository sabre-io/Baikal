<?php

/** @var Silex\Application $app */
use Baikal\Framework\Silex\Controller\AdminController;
use Baikal\Framework\Silex\Controller\DashboardController;
use Baikal\Framework\Silex\Controller\IndexController;

$controllerServices = [
    'index.controller' => IndexController::class,
    'admin.controller' => AdminController::class,
    'dashboard.controller' => DashboardController::class,
];

foreach ($controllerServices as $serviceName => $controllerClass) {
    $app[$serviceName] = $controllerClass;
}
