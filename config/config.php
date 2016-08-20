<?php
// This configuration file is automatically generated. Any changes made here may be overwritten.

return array (
  'version' => '0.5.0-dev',
  'caldav' => 
  array (
    'enabled' => true,
  ),
  'carddav' => 
  array (
    'enabled' => true,
  ),
  'auth' => 
  array (
    'type' => 'Digest',
    'realm' => 'BaikalDAV',
  ),
  'debug' => true,
  'pdo' => 
  array (
    'dsn' => 'sqlite:../Specific/db/db.sqlite',
    'username' => NULL,
    'password' => NULL,
  ),
);