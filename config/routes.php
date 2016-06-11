<?php

/** @var Silex\Application $app */

$app->get('/', 'index.controller:indexAction');
$app->get('/admin', 'dashboard.controller:indexAction');
