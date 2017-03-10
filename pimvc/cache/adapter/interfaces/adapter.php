<?php

/**
 * Lib_Cache_Adapter_Interface
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */

namespace lib\cache\adapter\interfaces;

interface adapter {

    public static function getInstance($name, $ttl);

    public function get($key);

    public function set($key, $value);

    public function delete($key);
}
