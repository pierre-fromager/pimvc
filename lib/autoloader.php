<?php

/**
 * Description of lib\autoloader
 *
 * @author pierrefromager
 */

namespace lib;

class autoloader {
    
    const AUTOLOAD_BOOT = '\lib\autoloader::load';
    const AUTOLOAD_BACKSLASH = '\\';
    const AUTOLOAD_SLASH = '/';
    const AUTOLOAD_PATH_UP = '/../';
    const AUTOLOAD_PHP_EXT = '.php';

    /**
     * register
     * 
     */
    public static function register() {
        spl_autoload_register(self::AUTOLOAD_BOOT);
    }

    /**
     * load
     * 
     * @param string $class
     */
    public static function load($class) {
        $class = ltrim($class, self::AUTOLOAD_BACKSLASH);
        $file = __DIR__ . self::AUTOLOAD_PATH_UP;
        if ($lastNsPos = strrpos($class, self::AUTOLOAD_BACKSLASH)) {
            $namespace = substr($class, 0, $lastNsPos);
            $class = substr($class, $lastNsPos + 1);
            $file .= str_replace(
                self::AUTOLOAD_BACKSLASH, 
                self::AUTOLOAD_SLASH
                , $namespace
            ) . self::AUTOLOAD_SLASH;
        }
        $file .= $class . self::AUTOLOAD_PHP_EXT;
        include $file;
    }

}
