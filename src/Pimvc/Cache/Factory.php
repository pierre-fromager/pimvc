<?php

/**
 *  Description of Pimvc\Cache\adapter\factory
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */

namespace Pimvc\Cache;

use \Pimvc\Cache\Adapter\File as fileAdapter;

class Factory {
    
    const CACHE_ADAPTER_FILE = 'File';
    const CACHE_ADAPTER_APC = 'Apc';
    const CACHE_ADAPTER_MEMCACHE = 'Memcache';
    const DEFAULT_TTL = 300;

    /**
     * get
     * 
     * @param string $type
     * @param string $name
     * @param int $ttl
     * 
     * @return mixed
     */
    public static function get($type, $name = '', $ttl = self::DEFAULT_TTL) {
        $type = ucfirst($type);
        switch ($type) {
            case self::CACHE_ADAPTER_FILE:
                return fileAdapter::getInstance($name, $ttl);
            case self::CACHE_ADAPTER_APC:
                return Lib_Cache_Adapter_Apc::getInstance($name, $ttl);
            case self::CACHE_ADAPTER_MEMCACHE:
                return Lib_Cache_Adapter_Memcache::getInstance($name, $ttl);
            default :
                return false;
        }
    }
}

