<?php

/**
 * DbPdomysqlAdapter returns Mysql instance
 * @author Pierre Fromager <pf@pier-infor.fr>
 */

namespace pimvc\db\adapter;

class mysql {

    const prefix = 'mysql:';
    const hostPrefix = 'host=';
    const dbNamePrefix = 'dbname=';
    const errorConnectFailed = 'Connexion échouée : ';

    protected static $dsn = null;
    protected static $params = null;
    protected static $_instance = null;

    private function __construct() {
        
    }

    private function __clone() {
        
    }

    /**
     * getInstance : returns Mysql Instance
     * @param array $params
     */
    public static function getInstance($params) {
        self::$params = $params;
        if (self::$_instance === null) {
            try {
                self::$_instance = mysql_connect(
                    $params['host'],
                    $params['user'],
                    $params['password']
                ) or die("Could not connect");
                mysql_select_db($params['dbname']) or die("Could not select database");
            } catch (Exception $e) {
                echo self::errorConnectFailed . $e->getMessage();
            }
        }
        return self::$_instance;
    }

}