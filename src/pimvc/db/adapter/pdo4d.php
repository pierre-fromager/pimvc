<?php

/**
 * DbPdo4dAdapter returns 4d Pdo instance
 * 
 * @see http://sources.4d.com/trac/4d_pdo4d/raw-attachment/wiki/PDOBuilds/pdo_4d_0.3_debian.zip
 * ports dispos 19812,19813,19814
 * 
 * @author Pierre Fromager <pf@pier-infor.fr>
 */

namespace pimvc\db\adapter;

class pdo4d
{
    const prefix = '4D:';
    const hostPrefix = 'host=';
    const dbNamePrefix = ';dbname=';
    const portPrefix = ';port=';
    const charsetPrefix = ';charset=';
    const errorConnectFailed = 'Echec de connexion : ';

    protected static $dsn = null;
    protected static $params = null;
    protected static $_instance = null;
    private function __construct(){}
    private function __clone(){}

    /**
     * hasValue
     * 
     * @param string $name
     * @return boolean 
     */
    private static function hasValue($name) {
        return (isset(self::$params[$name])  && !empty(self::$params[$name]));
    }
    
    /**
     * setDsn : returns dsn string
     * 
     * @param array $params
     */
    private static function setDsn() {
        $dbname = self::hasValue('dbname')
            ? self::dbNamePrefix . self::$params['dbname'] 
            : '';
         $port = self::hasValue('port') 
            ? self::portPrefix . self::$params['port'] 
            : '';
         $charset = self::hasValue('charset')
            ? self::charsetPrefix . self::$params['charset'] 
            : '';               
        self::$dsn = self::prefix 
            . self::hostPrefix . self::$params['host'] . ''
            . $port . $dbname . $charset;
    }

    /**
     * getOptions
     * 
     * @return array 
     */
    private static function getOptions() {
        return array(
            //PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
            PDO::ATTR_PERSISTENT => false
            , PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            //, PDO::ERRMODE_EXCEPTION => true
            , PDO::ATTR_CASE => PDO::CASE_LOWER
            , PDO::ATTR_EMULATE_PREPARES => false
                //, PDO::ATTR_CASE => PDO::CASE_NATURAL
        );
    }
    
    /**
     * getInstance : returns 4d Pdo Instance
     * 
     * @param array $params
     */
    public static function getInstance($params) {
        self::$params = $params;
        self::setDsn();
        self::$params['options'] = self::getOptions();
        //$options = (isset($params['options'])) ? $params['options'] : null;
        if (self::$_instance === null) {
            try {
                self::$_instance = new PDO(
                    self::$dsn
                    , self::$params['user']
                    , self::$params['password']
                    , self::$params['options']
                );
            } catch (PDOException $e) {
                echo self::errorConnectFailed . $e->getMessage();
                die;
            }
        }
        return self::$_instance;
    }
}