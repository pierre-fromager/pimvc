<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('date.timezone', 'Europe/Paris');
ini_set('register_globals', 0);

require_once __DIR__ . '/lib/app.php';

$routes = [
    '/^(home)$/' ,              // 1st group match controller with default action
    '/^(home)\/(.*?)(\?.*)/' ,  // 3rd group match ?a=1&b=2
    '/^(home)\/(.*?)(\/.*)/' ,  // 3rd group match /a/1/b/2
    '/^(home)\/(.*)$/' ,        // 1st group match controller 2nd match action
    '/^(stat)$/' ,
    '/^(stat)\/([a-zA-Z0-9_]{1,10})/' ,
    '/^api\/v1\/([a-zA-Z0-9_]{1,10})/' ,
    '/^api\/v1\/([a-zA-Z0-9_]{1,10})\/(\d*)/' ,
];

$app = (new lib\app($routes))->setPath(__DIR__)->run();