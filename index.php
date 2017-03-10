<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('date.timezone', 'Europe/Paris');
ini_set('register_globals', 0);

// Framework path
$fwkPath = __DIR__;
require_once $fwkPath . '/pimvc/autoloader.php';

$appPath = $fwkPath . '/app1';
$autoloader = new \pimvc\autoloader;
$autoloader->setAppPath($appPath)->register($fwkPath)->setCache();

(new app1\app(
    (new \pimvc\config())->setPath($appPath . '/config/')
        ->setEnv(\pimvc\config::ENV_DEV)
        ->load()
))->setPath($appPath)->run();

