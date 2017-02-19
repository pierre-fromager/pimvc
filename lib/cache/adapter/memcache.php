<?php

/**
 * Description of lib\cache\adapter\memcache
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */

namespace lib\cache\adapter;

use lib\cache\adapter\interfaces\adapter as cacheInterface;

class memcache implements cacheInterface {
    
    const CACHE_ADAPTER_TTL = 300;
    const CACHE_ADAPTER_EXTENSION = 'memcache';
    const CACHE_ADAPTER_HOST = 'localhost';
    const CACHE_ADAPTER_PORT = 11211;
    
    private static $_instance;
    private static $_memobj;
    private static $_error;
    private static $_ttl;
    
    /**
     * @see __construct
     * 
     * @param type $ttl 
     */
    private function __construct($ttl = self::CACHE_ADAPTER_TTL) {
        self::$_ttl = $ttl;
        $loadError = (!extension_loaded(self::CACHE_ADAPTER_EXTENSION))
            ? dl(self::CACHE_ADAPTER_EXTENSION.'.so')
            : false;
        self::$_error = !class_exists(ucfirst(self::CACHE_ADAPTER_EXTENSION)) && !$loadError;
        
        if (!self::$_error && self::$_memobj == null) {
            self::$_memobj = new Memcache;
            self::$_memobj->connect(
                self::CACHE_ADAPTER_HOST
                , self::CACHE_ADAPTER_PORT
            );
        } else {
            throw new Exception;
        }
    }
    
    /**
     * @see  __clone
     */
    private function __clone() {}
    
    /**
     * getInstance
     * 
     * @param int $ttl
     * @return \Lib_Cache_Adapter_Memcache 
     */
    public static function getInstance($name = '', $ttl = self::CACHE_ADAPTER_TTL) {
        if (!(self::$_instance instanceof Lib_Cache_Adapter_Memcache)){
             self::$_instance = new Lib_Cache_Adapter_Memcache();
        }
        return self::$_instance;
    }
    
    /**
     * get data from cache server

     * 
     * @param string $key 
     * @return mixed
     */
    public function get($key) {
        return self::$_memobj->get($key);
    }
    
    /**
     * expired
     * 
     * @param string $key
     * @return boolean 
     */
    public function expired($key) {
        $exist = $this->exists($key);
        $expired = !$exist;
        if (!empty($key) && $exist) {
            $list = $this->getExpirations($key);
            $expirationDate = time() - self::$_ttl;
            $expiration = $list['expiration'];
            $expired = $expirationDate > $expiration;
            unset($list);
        }
        return $expired;
    }
    
    /**
     * exists
     * 
     * @param string $key
     * @return boolean 
     */
    public function exists($key) {
        return (self::$_memobj->get($key) !== false);
    }

    /**
     * set saves data to cache server
     * 
     * @param string $key
     * @param mixed $value
     * @return boolean 
     */
    public function set($key, $value) {
        return self::$_memobj->set($key, $value, MEMCACHE_COMPRESSED, self::$_ttl);
    }
    
    /**
     * delete data from cache server
     * 
     * @param type $key
     * @return boolean 
     */
    public function delete($key) {
        return self::$_memobj->delete($key);
    }
    
    
    /**
     * getVersion
     * 
     * @return string 
     */
    public function getVersion() {
        return self::$_memobj->getVersion();
    }
     
    
    /**
     * getExpirations
     * 
     * @return array 
     */
    private function getExpirations($key = '') {
        $list = array();
        $allSlabs = self::$_memobj->getExtendedStats('slabs');
        $serverDsn = self::CACHE_ADAPTER_HOST . ':' . self::CACHE_ADAPTER_PORT;
        foreach ($allSlabs as $server => $slabs) {
            foreach ($slabs AS $slabId => $slabMeta) {
                if (is_numeric($slabId)) {                
                    $cdump = self::$_memobj->getExtendedStats('cachedump', (int) $slabId);
                    if (isset($cdump[$serverDsn]) && is_array($cdump[$serverDsn])) {
                        $entries = $cdump[$serverDsn];
                        foreach ($entries as $k => $v) {
                            $list[$k] = array('expiration' => $v[1]);
                        }
                    }
                }
            }
        }
        ksort($list);
        $result = (empty($key) || !isset($list[$key])) ? $list : $list[$key];
        unset($list);
        return $result;
    }

}
