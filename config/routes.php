<?php

/** @var Silex\Application $app */
$app->get('/', 'index.controller:indexAction')->bind('home');
$app->get('/admin', 'admin.dashboard.controller:indexAction')->bind('admin_dashboard');
$app->get('/admin/users', 'admin.user.controller:indexAction')->bind('admin_users');
$app->get('/admin/logout', 'admin.controller:logoutAction')->bind('admin_logout');
