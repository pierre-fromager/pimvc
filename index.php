<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('date.timezone', 'Europe/Paris');
ini_set('register_globals', 0);

// Framework path
$fwkPath = __DIR__;
require_once $fwkPath . '/lib/autoloader.php';

// App path
$appPath = $fwkPath . '/app1';

$autoloader = new \lib\autoloader;
$autoloader->setAppPath($appPath)->register($fwkPath)->setCache();

$app1 = (new app1\app(
    (new \lib\config())->setPath($appPath . '/config/')
        ->setEnv(\lib\config::ENV_DEV)
        ->load()
))->setPath($appPath)->run();
