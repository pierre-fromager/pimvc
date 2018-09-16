<?php

/**
 * pdopgsql returns Pgsql Pdo instance
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */

namespace Pimvc\Db\Adapter;

use Pimvc\Db\Adapter\Interfaces\Adapter as IAdapter;

class Pdopgsql implements IAdapter
{
    const PREFIX = 'pgsql:';
    const USER_PREFIX = 'user=';
    const PASSWORD_PREFIX = 'password=';
    const HOST_PREFIX = 'host=';
    const PORT_PREFIX = 'port=';
    const DB_NAME_PREFIX = 'dbname=';
    const ERR_CON_FAIL = 'Connexion échouée : ';

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
        $port = (isset(self::$params[self::_PORT])) ? self::$params[self::_PORT] : 5432;
        self::$dsn = self::prefix . self::dbNamePrefix . self::$params[self::_NAME]
            . ';' . self::HOST_PREFIX . self::$params[self::_HOST]
            . ';' . self::PORT_PREFIX . $port
            . ';' . self::USER_PREFIX . self::$params[self::_USER]
            . ';' . self::PASSWORD_PREFIX . self::$params[self::_PASSWORD];
    }

    /**
     * getInstance : returns Mysql Pdo Instance
     * @param array $params
     */
    public static function getInstance($params)
    {
        $options = self::getOptions();
        self::$params = $params;
        self::setDsn();
        if (self::$_instance === null) {
            try {
                self::$_instance = new \PDO(
                    self::$dsn,
                    $params[self::_USER],
                    $params[self::_PASSWORD],
                    $options
                );
            } catch (\PDOException $e) {
                echo self::ERR_CON_FAIL . $e->getMessage();
                die;
            }
        }
        return self::$_instance;
    }
    
    /**
     * getOptions
     *
     * @return array
     */
    private static function getOptions()
    {
        return array(
            //\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES ' . self::charset
            \PDO::ATTR_PERSISTENT => false
            , \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
            //, \PDO::ERRMODE_EXCEPTION => false
            , \PDO::ATTR_CASE => \PDO::CASE_LOWER
            , \PDO::ATTR_EMULATE_PREPARES => true
            , 'debug' => 2
            , 'portability' => 1
        );
    }
}
