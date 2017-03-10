<?php

/**
 * DbPdomysqlAdapter returns Mysql Pdo instance
 * @author Pierre Fromager <pf@pier-infor.fr>
 */

namespace pimvc\db\adapter;

class pdomysql {

    const charset = 'utf8';
    const prefix = 'mysql:';
    const hostPrefix = 'host=';
    const dbNamePrefix = 'dbname=';
    const errorConnectFailed = 'Connexion échouée : ';

    protected static $dsn = null;
    protected static $params = null;
    protected static $_instance = null;

    /**
     * __construct
     * 
     */
    private function __construct() {}

    /**
     * __clone
     * 
     */
    private function __clone() {}

    /**
     * setDsn : returns dsn string
     * @param array $params
     */
    private static function setDsn() {
        self::$dsn = self::prefix . self::hostPrefix . self::$params['host'] 
            . ';' . self::dbNamePrefix . self::$params['name'];
    }

    /**
     * getOptions
     * 
     * @return array 
     */
    private static function getOptions() {
        return array(
            \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES ' . self::charset
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
    public static function getInstance($params) {
        self::$params = $params;
        self::$params['options'] = self::getOptions();
        self::setDsn();
        if (self::$_instance === null) {
            try {
                self::$_instance = new \PDO(
                    self::$dsn,
                    self::$params['user'],
                    self::$params['password'],
                    self::$params['options']
                );
            } catch (\PDOException $e) {
                echo self::errorConnectFailed . $e->getMessage();
            }
        }
        return self::$_instance;
    }

}