<?php

/**
 * class Cache is a cache file based mechanism
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 * @deprecated r.55
 * @see Lib_Cache_Factory with file adapter
 * @uses class logger
 */

namespace Pimvc;

class Cache {

    const DEFAULT_CACHE_EXPIRATION = 300; // = 5 minutes * 60 secondes
    const DEFAULT_CACHE_PATH = '/cache/';
    const DEFAULT_CACHE_EXT = '.tmp';
    const DEBUG = false;
    const DEFAULT_ADPATER = \Pimvc\Cache\adapter\file::class;

    private $adapter = '';
    private $name = '';
    private $expiration = 0;
    private $cache = null;
    private $path = null;
    private $logger = null;

    /**
     * Constructor set page name and time to live with expiration value.
     * 
     * @param string $page
     * @param int $expiration 
     */
    public function __construct(
            $name
            , $expiration = self::DEFAULT_CACHE_EXPIRATION
            , $adapter = self::DEFAULT_ADPATER
        ) {
        $this->adapter = $adapter;
        $appPath = \Pimvc\App::getInstance()->getPath();
        $this->setPath($appPath . self::DEFAULT_CACHE_PATH);
        $this->setName($name);        
        $this->expiration = $expiration;
        if (self::DEBUG) {
            $this->logger = Logger::getInstance(
                $appPath . LOG_DIR, Logger::DEBUG
            );
        }
    }

    /**
     * load cache file content.
     */
    protected function load() {
        $this->cache = unserialize(file_get_contents($this->path . $this->name));
    }
    
    /**
     * get loads and returns cache content
     * 
     * @return string 
     */
    public function get() {
        if (self::DEBUG) {
            $this->logger->logDebug(
                __METHOD__
                , basename($this->path . $this->name)
            );
        }
        $this->load();
        return $this->cache;
    }

    /**
     * exist return true if file or directory exists.
     * 
     * @return boolean 
     */
    protected function exist($filename) {
        return file_exists($filename);
    }

    /**
     * expired return true if cache file date is greater than now minus expiration.
     * 
     * @return boolean 
     */
    public function expired() {
        $filename = $this->path . $this->name;
        $expirationDate = time() - $this->expiration;
        $fileDate = ($this->exist($filename)) 
            ? filemtime($filename) 
            : false;
        return $expirationDate > $fileDate;
    }
    
    /**
     * fill the cache file for a given $cache content.
     * 
     * @param type $cache
     * @return mixed
     */
    public function set($cache) {
        $filename = $this->path . $this->name;
        if (self::DEBUG) {
            $this->logger->logDebug(__METHOD__, basename($filename));
        }
        $serializedCache = serialize($cache);
        if (!$this->exist($this->path)) {
            mkdir($this->path, 0777, true);
            chmod(dirname($this->path,2), 0777, true);
        }
        
        file_put_contents($filename, $serializedCache);
        $this->cache = $cache;
        return $this->cache;
    }
    
    /**
     * setName
     * 
     * @param string $name 
     */
    public function setName($name) {
        $this->name = $name  . self::DEFAULT_CACHE_EXT;
    }
    
    /**
     * getName
     * 
     * @return string $name 
     */
    public function getName() {
        return $this->name;
    }
    
    /**
     * setPath
     * 
     * @param string $path 
     */
    public function setPath($path) {
        $this->path = $path;
    }
    
    /**
     * getPath
     * 
     * @return string $path 
     */
    public function getPath() {
        return $this->path;
    }
    
     /**
     * setExpiration
     * 
     * @param int $expiration 
     */
    public function setExpiration($expiration) {
        $this->expiration = $expiration;
    }
    
    /**
     * getExpiration
     * 
     * @return int $expiration 
     */
    public function getExpiration() {
        return $this->expiration;
    }
    
    /**
     * delete
     * 
     */
    public function delete() {
        $filename = $this->path . $this->name;
        if (file_exists($filename)) {
            unlink($filename);
        }
    }
    
    

}

