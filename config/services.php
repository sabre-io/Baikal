<?php

/** @var Silex\Application $app */
use Baikal\Framework\Silex\ControllerResolver;
use Baikal\Infrastructure\Repository\PdoUserRepository;

$app->register(new Silex\Provider\TwigServiceProvider(), [
    'twig.path' => $baseDir . '/views/',
]);

$app->register(new Silex\Provider\Psr7ServiceProvider());

$app['resolver'] = function($app) {
    return new ControllerResolver($app);
};

$app['pdo'] = function($app) {
    return new PDO($app['pdo.dsn'], $app['pdo.username'], $app['pdo.password']);
};

$app['admin.user.repository'] = function($app) {
    return new PdoUserRepository($app['pdo']);
};
