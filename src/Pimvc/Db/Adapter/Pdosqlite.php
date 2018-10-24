<?php

/**
 * pdosqlite returns Sqlite2 Pdo instance
 * @author Pierre Fromager <pf@pier-infor.fr>
 */

namespace Pimvc\Db\Adapter;

use Pimvc\Db\Adapter\Interfaces\Adapter as IAdapter;

class Pdosqlite implements IAdapter
{
    const PREFIX_SQLITE = 'sqlite:';

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
     * setDsn : returns dsn string
     * @param array $params
     */
    private static function setDsn()
    {
        self::$dsn = self::PREFIX_SQLITE . self::$params[self::_FILE];
    }

    /**
     * getInstance : returns Mysql Pdo Instance
     * @param array $params
     */
    public static function getInstance(array $params)
    {
        self::$params = $params;
        self::setDsn();
        if (self::$_instance === null) {
            try {
                self::$_instance = new \PDO(self::$dsn);
            } catch (\PDOException $e) {
                echo self::ERR_CON_FAIL . $e->getMessage();
                die;
            }
        }
        return self::$_instance;
    }
}
