<?php
/**
 * DbPdo4dAdapter returns 4d Pdo instance
 *
 * @see http://sources.4d.com/trac/4d_pdo4d/raw-attachment/wiki/PDOBuilds/pdo_4d_0.3_debian.zip
 * ports dispos 19812,19813,19814
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */
namespace Pimvc\Db\Adapter;

use Pimvc\Db\Adapter\Interfaces\Adapter as IAdapter;

class Pdo4d implements IAdapter
{

    const PREFIX_4D = '4D:';
    //const HOST_PREFIX = 'host=';
    //const DB_NAME_PREFIX = ';dbname=';
    const PORT_PREFIX = ';port=';
    const CHARSET_PREFIX = ';charset=';
    //const ERR_CON_FAIL = 'Echec de connexion : ';

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
     * hasValue
     *
     * @param string $name
     * @return boolean
     */
    private static function hasValue($name)
    {
        return (isset(self::$params[$name]) && !empty(self::$params[$name]));
    }

    /**
     * setDsn : returns dsn string
     *
     * @param array $params
     */
    private static function setDsn()
    {
        $dbname = self::hasValue('dbname') ? self::dbNamePrefix . self::$params['dbname'] : '';
        $port = self::hasValue(self::_PORT) ? self::PORT_PREFIX . self::$params[self::_PORT] : '';
        $charset = self::hasValue('charset') ? self::CHARSET_PREFIX . self::$params['charset'] : '';
        self::$dsn = self::PREFIX_4D
            . self::HOST_PREFIX . self::$params[self::_HOST] . ''
            . $port . $dbname . $charset;
    }

    /**
     * getOptions
     *
     * @return array
     */
    private static function getOptions()
    {
        return array(
            //PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
            \PDO::ATTR_PERSISTENT => false
            , \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
            //, PDO::ERRMODE_EXCEPTION => true
            , \PDO::ATTR_CASE => \PDO::CASE_LOWER
            , \PDO::ATTR_EMULATE_PREPARES => false
            //, PDO::ATTR_CASE => PDO::CASE_NATURAL
        );
    }

    /**
     * getInstance : returns 4d Pdo Instance
     *
     * @param array $params
     */
    public static function getInstance(array $params)
    {
        self::$params = $params;
        self::setDsn();
        self::$params[self::_OPTIONS] = self::getOptions();
        //$options = (isset($params['options'])) ? $params['options'] : null;
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
                die;
            }
        }
        return self::$_instance;
    }
}
