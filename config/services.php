<?php

/** @var Silex\Application $app */
use Baikal\Framework\Silex\ControllerResolver;
use Baikal\Infrastructure\Repository\PdoUserRepository;

$app->register(new Silex\Provider\TwigServiceProvider(), [
    'twig.path' => __DIR__ . '/../views/',
]);

$app['resolver'] = function($app) {
    return new ControllerResolver($app);
};

$app['pdo'] = function($app) {
    return new PDO($app['pdo.dsn'], $app['pdo.username'], $app['pdo.password']);
};

$app['admin.user.repository'] = function($app) {
    return new PdoUserRepository($app['pdo']);
};
