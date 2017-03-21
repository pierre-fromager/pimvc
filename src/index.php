<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('date.timezone', 'Europe/Paris');
ini_set('register_globals', 0);

// Framework path
$fwkPath = __DIR__;
require_once $fwkPath . '/Pimvc/Autoloader.php';

$appPath = $fwkPath . '/App1';
$autoloader = new \Pimvc\Autoloader;
$autoloader->setAppPath($appPath)->register($fwkPath)->setCache();

(new App1\App(
    (new \Pimvc\Config())->setPath($appPath . '/config/')
        ->setEnv(\Pimvc\Config::ENV_DEV)
        ->load()
))->setPath($appPath)->run();

