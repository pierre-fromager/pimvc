<?php

/**
 * Description of lib\interfaces\autoloader
 *
 * @author pierrefromager
 */

namespace lib\interfaces;

interface autoloader {

    const AUTOLOAD_BOOT = 'load';
    const AUTOLOAD_BACKSLASH = '\\';
    const AUTOLOAD_SLASH = '/';
    const AUTOLOAD_PATH_UP = '/../';
    const AUTOLOAD_PHP_EXT = '.php';
    const AUTOLOAD_CACHE_FILE = '/tmp/cache.json';

    public function __construct();

    public function register($rootPath);

    public function setCache();
}
