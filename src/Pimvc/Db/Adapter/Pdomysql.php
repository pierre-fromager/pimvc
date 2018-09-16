<?php

/**
 * Pimvc\Db\Adapter\Pdomysql
 *
 * returns Mysql Pdo instance
 * @author Pierre Fromager <pf@pier-infor.fr>
 */

namespace Pimvc\Db\Adapter;

use Pimvc\Db\Adapter\Interfaces\Adapter as IAdapter;

class Pdomysql implements IAdapter
{
    protected static $dsn = null;
    protected static $params = null;
    protected static $_instance = null;

    /**
     * __construct
     *
     */
    private function __construct()
    {
    }

    /**
     * __clone
     *
     */
    private function __clone()
    {
    }

    /**
     * setDsn : returns dsn string
     * @param array $params
     */
    private static function setDsn()
    {
        self::$dsn = self::PREFIX . self::HOST_PREFIX . self::$params[self::_HOST]
            . ';' . self::DB_NAME_PREFIX . self::$params[self::_NAME];
    }

    /**
     * getOptions
     *
     * @return array
     */
    private static function getOptions()
    {
        return array(
            \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES ' . self::CHARSET
            , \PDO::ATTR_PERSISTENT => false
            , \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
            //, \PDO::ERRMODE_EXCEPTION => false
            , \PDO::ATTR_CASE => \PDO::CASE_LOWER
            , \PDO::ATTR_EMULATE_PREPARES => true
        );
    }

    /**
     * getInstance : returns Mysql Pdo Instance
     *
     * @param array $params
     */
    public static function getInstance(array $params)
    {
        self::$params = $params;
        self::$params[self::_OPTIONS] = self::getOptions();
        self::setDsn();
        if (self::$_instance === null) {
            try {
                self::$_instance = new \PDO(
                    self::$dsn,
                    self::$params[self::_USER],
                    self::$params[self::_PASSWORD],
                    self::$params[self::_OPTIONS]
                );
            } catch (\PDOException $e) {
                echo self::ERR_CON_FAIL . $e->getMessage();
            }
        }
        return self::$_instance;
    }
}
