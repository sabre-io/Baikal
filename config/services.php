<?php

/** @var Silex\Application $app */
use Baikal\Framework\Silex\ControllerResolver;

$app->register(new Silex\Provider\TwigServiceProvider(), [
    'twig.path' => $baseDir . '/views/',
]);

$app->register(new Silex\Provider\Psr7ServiceProvider());

$app['resolver'] = function($app) {
    return new ControllerResolver($app);
};
