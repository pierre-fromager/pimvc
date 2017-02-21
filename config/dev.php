<?php

return array(
    'routes' => [
        '/^(home)$/', // 1st group match controller with default action
        '/^(home)\/(.*?)(\?.*)/', // 3rd group match ?a=1&b=2
        '/^(home)\/(.*?)(\/.*)/', // 3rd group match /a/1/b/2
        '/^(home)\/(.*)$/', // 1st group match controller 2nd match action
        '/^(user)$/', // 1st group match controller with default action
        '/^(user)\/(.*?)(\?.*)/', // 3rd group match ?a=1&b=2
        '/^(user)\/(.*?)(\/.*)/', // 3rd group match /a/1/b/2
        '/^(user)\/(.*)$/', // 1st group match controller 2nd match action
        '/^(stat)$/',
        '/^(stat)\/([a-zA-Z0-9_]{1,10})/',
        '/^api\/v1\/([a-zA-Z0-9_]{1,10})/',
        '/^api\/v1\/([a-zA-Z0-9_]{1,10})\/(\d*)/',
    ],
    'dbPool' => [
        'db1' => [
            'adapter' => 'PdoMysql',
            'name' => 'rdmax',
            'host' => '192.168.1.48',
            'user' => 'pierre',
            'port' => '3306',
            'password' => 'pierre'
        ] ,
        'db2' => [
            'adapter' => 'PdoPgsql',
            'name' => 'rdmax',
            'host' => 'localhost',
            'user' => 'pierre',
            'port' => '5432',
            'password' => 'pierre'
        ]
    ]
);
