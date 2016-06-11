<?php

/** @var Silex\Application $app */
use Baikal\Framework\Silex\Controller\Admin\UserController;
use Baikal\Framework\Silex\Controller\AdminController;
use Baikal\Framework\Silex\Controller\Admin\DashboardController;
use Baikal\Framework\Silex\Controller\IndexController;

$controllerServices = [
    'index.controller' => IndexController::class,
    'admin.controller' => AdminController::class,
    'admin.dashboard.controller' => DashboardController::class,
    'admin.user.controller' => UserController::class,
];

foreach ($controllerServices as $serviceName => $controllerClass) {
    $app[$serviceName] = $controllerClass;
}
