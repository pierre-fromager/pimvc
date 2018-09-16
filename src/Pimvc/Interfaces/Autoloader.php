<?php

/**
 * Description of Pimvc\Interfaces\Autoloader
 *
 * @author pierrefromager
 */

namespace Pimvc\Interfaces;

interface Autoloader
{
    const AUTOLOAD_BOOT = 'load';
    const AUTOLOAD_BACKSLASH = '\\';
    const AUTOLOAD_SLASH = '/';
    const AUTOLOAD_PATH_UP = '/../';
    const AUTOLOAD_PHP_EXT = '.php';
    const AUTOLOAD_CACHE_FILE = '/tmp/cache.json';

    public function __construct();
    
    public function setAppPath($path);
            
    public function register($rootPath);

    public function setCache();
}
