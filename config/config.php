<?php

return [
    'version' => '0.5.0-dev',

    'caldav' => [
        'enabled' => true,
    ],
    'carddav' => [
        'enabled' => true,
    ],

    'auth' => [
        'type' => 'Digest',
        'realm' => 'BaikalDAV',
    ],

    'debug' => true,

    'pdo' => [
        'dsn' => 'sqlite:../Specific/db/db.sqlite',
        'username' => null,
        'password' => null,
    ]

];
