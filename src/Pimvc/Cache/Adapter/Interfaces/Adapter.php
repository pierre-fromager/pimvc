<?php

/**
 * Lib_Cache_Adapter_Interface
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */

namespace Pimvc\Cache\Adapter\Interfaces;

interface Adapter
{
    public static function getInstance($name, $ttl);

    public function get($key);

    public function set($key, $value);

    public function delete($key);
}
