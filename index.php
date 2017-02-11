<?php

declare(strict_types = 1);

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('date.timezone', 'Europe/Paris');
ini_set('register_globals', 0);

require_once __DIR__ . '/lib/config.php';
require_once __DIR__ . '/lib/app.php';

$app = (new lib\app(
    (new \lib\config())->setPath(__DIR__ . '/config/')->setEnv(\lib\config::ENV_DEV)->load()
))->setPath(__DIR__)->run();
