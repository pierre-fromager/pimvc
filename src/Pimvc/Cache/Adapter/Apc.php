<?php

/**
 * Description of Pimvc\Cache\adapter\apc
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */

namespace Pimvc\Cache\Adapter;

use Pimvc\Cache\adapter\interfaces\adapter as cacheInterface;

class Apc implements cacheInterface {

    const CACHE_ADAPTER_TTL = 300;
    const CACHE_ADAPTER_EXTENSION = 'apc';
    
    private static $_instance;
    private static $_error;
    private static $_ttl;

    /**
     *
     * @param int $ttl 
     */
    private function __construct($ttl = self::CACHE_ADAPTER_TTL){
        self::$_ttl = $ttl;
        self::$_error = extension_loaded(self::CACHE_ADAPTER_EXTENSION);
    }
    
    /**
     * __clone
     */
    private function __clone() {}

    /**
     * getInstance
     * 
     * @return \Lib_Cache_Adapter_Apc
     */
    public static function getInstance() {
        if (!(self::$_instance instanceof Lib_Cache_Adapter_Apc)){
             self::$_instance = new Lib_Cache_Adapter_Apc(
                $ttl = self::CACHE_ADAPTER_TTL
             );
        }
        return self::$_instance;
    }

    /**
     * get
     * 
     */
    public function get($key) {
        $existValue = apc_fetch($key, false);
        return ($existValue) ? $existValue : null;
    }

    /**
     * set
     * 
     */
    public function set($key, $value) {
      return apc_store($key, $value, self::$_ttl);  
    }
    
    /**
     * delete
     * 
     */
    public function delete($key) {
        $exists = apc_fetch($key, false);
        return ($exists) ? apc_delete($key) : true;
    }
    
    /**
     * exists
     * 
     * @param type $key
     * @return type 
     */
    public function exists($key) {
        return apc_exists($key);
    }


    /**
     * expired
     * 
     * @param string $key
     * @return int|boolean 
     */
    public function expired($key) {
        $cache = apc_cache_info('user');
        $cacheList = $cache['cache_list'];
        foreach ($cacheList as $entry) {
            if ($entry['info'] != $key) {
                continue;
            }
            if ($entry['ttl'] == 0) {
                return 0;
            }
            $expire = $entry['creation_time'] + $entry['ttl'];
            return $expire;
        }
        unset($cache);
        return false;
    }
    
    /**
     * Return the filling percentage of the backend storage
     *
     * @throws Zend_Cache_Exception
     * @return int integer between 0 and 100
     */
    public function getFillingPercentage()
    {
        $mem = apc_sma_info(true);
        $memSize    = $mem['num_seg'] * $mem['seg_size'];
        $memAvailable= $mem['avail_mem'];
        $memUsed = $memSize - $memAvailable;
        if ($memSize == 0) {
            throw ('can\'t get apc memory size');
        }
        if ($memUsed > $memSize) {
            return 100;
        }
        return ((int) (100. * ($memUsed / $memSize)));
    }

    /**
     * Return an array of metadatas for the given cache id
     *
     * The array must include these keys :
     * - expire : the expire timestamp
     * - tags : a string array of tags
     * - mtime : timestamp of last modification time
     *
     * @param string $id cache id
     * @return array array of metadatas (false if the cache id is not found)
     */
    public function getMetadatas($key)
    {
        $tmp = apc_fetch($key);
        if (is_array($tmp)) {
            $data = $tmp[0];
            $mtime = $tmp[1];
            if (!isset($tmp[2])) {
                // because this record is only with 1.7 release
                // if old cache records are still there...
                return false;
            }
            $lifetime = $tmp[2];
            return array(
                'expire' => $mtime + $lifetime,
                'tags' => array(),
                'mtime' => $mtime
            );
        }
        return false;
    }

}
