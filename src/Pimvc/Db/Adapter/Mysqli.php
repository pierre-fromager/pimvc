<?php

/**
 * DbPdomysqlAdapter returns Mysql instance
 * @author Pierre Fromager <pf@pier-infor.fr>
 */

namespace Pimvc\Db\Adapter;

class Mysqli
{
    const prefix = 'mysql:';
    const hostPrefix = 'host=';
    const dbNamePrefix = 'dbname=';
    const errorConnectFailed = 'Connexion échouée : ';

    protected static $dsn = null;
    protected static $params = null;
    protected static $_instance = null;
    private function __construct(){}
    private function __clone(){}

    /**
     * getInstance : returns Mysql Instance
     * @param array $params
     */
    public static function getInstance($params)
    {
        self::$params = $params;
        //self::setDsn();
        if(self::$_instance === null){
            try {
                //self::$_instance = new PDO(self::$dsn, $params['user'], $params['password'], $params['options']);
                //self::$_instance = mysql_connect($params['host'], $params['user'], $params['password']) or die("Could not connect");
                self::$_instance = new mysqli($params['host'], $params['user'], $params['password'], $params['dbname']);
                //mysql_select_db($params['dbname']) or die("Could not select database");
                //echo 'con done';
            }
            catch (Exception $e) {echo self::errorConnectFailed. $e->getMessage();}
        }
        return self::$_instance;
    }
}