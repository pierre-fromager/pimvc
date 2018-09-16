<?php

/**
 * Pimvc\Db\Adapter\Mysqli
 *
 * returns Mysql instance
 * @author Pierre Fromager <pf@pier-infor.fr>
 */

namespace Pimvc\Db\Adapter;

use Pimvc\Db\Adapter\Interfaces\Adapter as IAdapter;

class Mysqli implements IAdapter
{

    protected static $dsn = null;
    protected static $params = null;
    protected static $_instance = null;

    private function __construct()
    {
    }
    private function __clone()
    {
    }

    /**
     * getInstance : returns Mysql Instance
     * @param array $params
     */
    public static function getInstance($params)
    {
        self::$params = $params;
        if (self::$_instance === null) {
            try {
                self::$_instance = new mysqli(
                    $params[self::_HOST],
                    $params[self::_USER],
                    $params[self::_PASSWORD],
                    $params['dbname']
                );
            } catch (\Exception $e) {
                echo self::ERR_CON_FAIL . $e->getMessage();
            }
        }
        return self::$_instance;
    }
}
