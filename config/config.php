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
  // password is 'admin'. Temporary though
  'admin_passwordhash' => '142ff212f9ed2f8f8b5e7b96f6929f78',
);
