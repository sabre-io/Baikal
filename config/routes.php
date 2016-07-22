<?php

/** @var Silex\Application $app */
$app->get('/', 'index.controller:indexAction')->bind('home');
$app->get('/admin', 'admin.dashboard.controller:indexAction')->bind('admin_dashboard');

$app->get('/admin/users', 'admin.user.controller:indexAction')->bind('admin_users');
$app->get('/admin/users/new', 'admin.user.controller:createAction')->bind('admin_users_create');
$app->post('/admin/users/new', 'admin.user.controller:postCreateAction')->bind('admin_users_create_post');
$app->get('/admin/users/{userId}', 'admin.user.controller:editAction')->bind('admin_users_edit');

$app->get('/admin/user/{userName}/addressbooks', 'admin.user.controller:addressbookAction')->bind('admin_user_addressbooks');
$app->get('/admin/user/{userName}/calendars', 'admin.user.controller:calendarAction')->bind('admin_user_calendars');
$app->get('/admin/user/{userName}/edit', 'admin.user.controller:editAction')->bind('admin_user_edit');
$app->get('/admin/user/{userName}/delete', 'admin.user.controller:deleteAction')->bind('admin_user_delete');
$app->post('/admin/user/{userName}/delete', 'admin.user.controller:postDeleteAction')->bind('admin_user_delete_post');

$app->get('/admin/logout', 'admin.controller:logoutAction')->bind('admin_logout');
