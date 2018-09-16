<?php

/**
 * Pimvc\Db\Adapter\Mysql returns Mysql instance
 * @author Pierre Fromager <pf@pier-infor.fr>
 */

namespace Pimvc\Db\Adapter;

use Pimvc\Db\Adapter\Interfaces\Adapter as IAdapter;

class Mysql implements IAdapter
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
                self::$_instance = mysql_connect(
                    $params[self::_HOST],
                    $params[self::_USER],
                    $params[self::_PASSWORD]
                ) or die("Could not connect");
                mysql_select_db($params['dbname']) or die("Could not select database");
            } catch (\Exception $e) {
                echo self::ERR_CON_FAIL . $e->getMessage();
            }
        }
        return self::$_instance;
    }
}
