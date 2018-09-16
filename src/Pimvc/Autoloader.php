<?php

/**
 * Description of Pimvc\Autoloader
 *
 * @author pierrefromager
 */

namespace Pimvc;

require_once __DIR__ . DIRECTORY_SEPARATOR . 'Interfaces/Autoloader.php';

class Autoloader implements Interfaces\Autoloader
{
    private $rootPath;
    private $cache;
    private $appPath;

    /**
     * __construct
     *
     * @return $this
     */
    public function __construct()
    {
        return $this;
    }
    
    /**
     * setAppPath
     *
     * @param string $path
     */
    public function setAppPath($path)
    {
        $this->appPath = $path;
        return $this;
    }

    /**
     * register
     *
     */
    public function register($rootPath)
    {
        $this->rootPath = $rootPath;
        spl_autoload_extensions('.php');
        spl_autoload_register(array($this, self::AUTOLOAD_BOOT));
        return $this;
    }

    /**
     * setCache
     *
     * @return $this
     */
    public function setCache()
    {
        $this->cache = json_decode(
            file_get_contents($this->getCacheFilename()),
            true
        );
        return $this;
    }

    /**
     * getCacheFilename
     *
     * @return string
     */
    private function getCacheFilename()
    {
        return $this->appPath . self::AUTOLOAD_CACHE_FILE;
    }

    /**
     * getFromCache
     *
     * @param string $class
     * @return string | boolean
     */
    private function getFromCache($class)
    {
        return (isset($this->cache[$class])) ? $this->cache[$class] : false;
    }

    /**
     * saveCache
     *
     */
    private function saveCache()
    {
        file_put_contents(
            $this->getCacheFilename(),
            json_encode($this->cache, JSON_PRETTY_PRINT)
        );
    }

    /**
     * load
     *
     * @param string $class
     */
    private function load($class)
    {
        if (!$file = $this->getFromCache($class)) {
            $file = $this->build($class);
            $this->cache[$class] = $file;
            $this->saveCache();
        }
        require_once $file;
    }

    /**
     * build
     *
     * @param string $class
     * @return string
     */
    private function build($class)
    {
        $class = ltrim($class, self::AUTOLOAD_BACKSLASH);
        $file = $this->rootPath . DIRECTORY_SEPARATOR;
        if ($lastNsPos = strrpos($class, self::AUTOLOAD_BACKSLASH)) {
            $namespace = substr($class, 0, $lastNsPos);
            $class = substr($class, $lastNsPos + 1);
            $file .= str_replace(
                self::AUTOLOAD_BACKSLASH,
                self::AUTOLOAD_SLASH,
                $namespace
            ) . self::AUTOLOAD_SLASH;
        }
        $file .= $class . self::AUTOLOAD_PHP_EXT;
        return $file;
    }
}
