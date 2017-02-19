<?php

/**
 * pdopgsql returns Pgsql Pdo instance
 * 
 * @author Pierre Fromager <pf@pier-infor.fr>
 */

namespace lib\db\adapter;

class pdopgsql
{
    const prefix = 'pgsql:';
    const userPrefix = 'user=';
    const passwordPrefix = 'password=';
    const hostPrefix = 'host=';
    const defaultPort = 5432;
    const portPrefix = 'port=';
    const dbNamePrefix = 'dbname=';
    const errorConnectFailed = 'Connexion échouée : ';

    protected static $dsn = null;
    protected static $params = null;
    protected static $_instance = null;
    private function __construct(){}
    private function __clone(){}

    /**
     * setDsn : returns dsn string
     * @param array $params
     */
    private static function setDsn() {
        $port = (isset(self::$params['port'])) 
            ? self::$params['port'] 
            : self::defaultPort;
        self::$dsn = self::prefix . self::dbNamePrefix . self::$params['name'] 
            . ';' . self::hostPrefix . self::$params['host']
            . ';' . self::portPrefix . $port
            . ';' . self::userPrefix . self::$params['user']
            . ';' . self::passwordPrefix . self::$params['password'];    
    }

    /**
     * getInstance : returns Mysql Pdo Instance
     * @param array $params
     */
    public static function getInstance($params) {
        $options = self::getOptions();
        self::$params = $params;
        self::setDsn();
        if (self::$_instance === null) {
            try {
                self::$_instance = new \PDO(
                    self::$dsn
                    , $params['user']
                    , $params['password']
                    , $options
                );
            } catch (\PDOException $e) {
                echo self::errorConnectFailed . $e->getMessage();die;
            }
        }
        return self::$_instance;
    }
    
    /**
     * getOptions
     * 
     * @return array 
     */
    private static function getOptions() {
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