<?php

declare(strict_types = 1);

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('date.timezone', 'Europe/Paris');
ini_set('register_globals', 0);

$there = __DIR__;

require_once $there . '/lib/autoloader.php';

$autoloader = (new \lib\autoloader())->register($there)->setCache();

$app = (new lib\app(
    (new \lib\config())->setPath($there . '/config/')
        ->setEnv(\lib\config::ENV_DEV)
        ->load()
))->setPath($there)->run();