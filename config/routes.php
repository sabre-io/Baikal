<?php

/** @var Silex\Application $app */
$app->get('/', 'index.controller:indexAction')->bind('home');
$app->get('/admin', 'dashboard.controller:indexAction')->bind('dashboard');
$app->get('/admin/logout', 'admin.controller:logoutAction')->bind('logout');
