<?php

$app['baseurl'] = '/';
$app['version'] = '0.6.0-dev';
$app['pagetitle'] = 'Baïkal ' . $app['version'] . ' Web Admin';

$app['caldav_enable'] = true;
$app['carddav_enable'] = true;

$app['debug'] = true;

$app['pdo.dsn'] = 'sqlite:foo.db';
$app['pdo.username'] = 'baikal';
$app['pdo.password'] = 'baikal';
