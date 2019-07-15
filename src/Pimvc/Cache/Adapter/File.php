<?php

/**
 * Description of Pimvc\Cache\adapter\file
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */
namespace Pimvc\Cache\Adapter;

use Pimvc\Cache\Adapter\Interfaces\Adapter as CacheInterface;

class File implements CacheInterface
{
    const CACHE_ADAPTER_TTL = 300;
    const DEFAULT_CACHE_PATH = '/cache/';
    const DEFAULT_CACHE_EXT = '.tmp';
    const DEBUG = false;
    
    private static $_instance = null;
    private static $_name;
    private static $_path;
    private static $_error;
    private static $_ttl;
    
    private $cache = null;
    private $logger = null;

    /**
     * __construct
     *
     * @param string $name
     * @param int $ttl
     */
    private function __construct($name, $ttl = self::CACHE_ADAPTER_TTL)
    {
        $appPath = \Pimvc\App::getInstance()->getPath();
        $this->setPath($appPath . self::DEFAULT_CACHE_PATH);
        $this->setName($name);
        self::$_ttl = $ttl;
    }
    
    /**
     * __clone
     */
    private function __clone()
    {
    }

    /**
     * getInstance
     *
     * @return \Lib_Cache_Adapter_File
     */
    public static function getInstance($name, $ttl = self::CACHE_ADAPTER_TTL)
    {
        self::$_name = $name;
        self::$_ttl = $ttl;
        if (self::DEBUG) {
            $this->logger = Logger::getInstance(APP_PATH . LOG_DIR, Logger::DEBUG);
        }
        if (!self::$_instance) {
            self::$_instance = new self($name, $ttl);
        }
        return self::$_instance;
    }
    
    /**
     * exist return true if file or directory exists.
     *
     * @return boolean
     */
    protected function exist($filename)
    {
        return file_exists($filename);
    }

    /**
     * expired return true if cache file date is greater than now minus expiration.
     *
     * @return boolean
     */
    public function expired()
    {
        $filename = $this->getFilename();
        $expirationDate = time() - self::$_ttl;
        $fileDate = ($this->exist($filename))
            ? filemtime($filename)
            : false;
        return $expirationDate > $fileDate;
    }
    
    /**
     * load cache file content.
     */
    protected function load()
    {
        $this->cache = unserialize(file_get_contents($this->getFilename()));
    }
    
    /**
     * get loads and returns cache content
     *
     * @return string
     */
    public function get($key = '')
    {
        if (self::DEBUG) {
            $this->logger->logDebug(__METHOD__, basename($this->getFilename()));
        }
        $this->load();
        return $this->cache;
    }

    /**
     * getFilename
     *
     * @return string
     */
    private function getFilename()
    {
        return self::$_path . self::$_name;
    }
    
    /**
     * set
     *
     */
    public function set($key = '', $value = '')
    {
        $filename = $this->getFilename();
        if (self::DEBUG) {
            $this->logger->logDebug(__METHOD__, basename($filename));
        }
        if ($key && !$value) {
            $value = $key;
        }
        $serializedCache = serialize($value);
        if (!$this->exist(self::$_path)) {
            mkdir(self::$_path, 0777);
        }
        
        file_put_contents($filename, $serializedCache);
        $this->cache = $value;
        return $this->cache;
    }
    
    /**
     * setName
     *
     * @param string $name
     */
    public function setName($name)
    {
        self::$_name = $name  . self::DEFAULT_CACHE_EXT;
    }
    
    /**
     * getName
     *
     * @return string $name
     */
    public function getName()
    {
        return self::$_name;
    }
    
    /**
     * setPath
     *
     * @param string $path
     */
    public function setPath($path)
    {
        self::$_path = $path;
    }
    
    /**
     * getPath
     *
     * @return string $path
     */
    public function getPath()
    {
        return self::$_path;
    }

    public function delete($key)
    {
    }
}
