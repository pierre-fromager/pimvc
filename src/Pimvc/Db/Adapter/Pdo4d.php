<?php
/**
 * Pimvc\Db\Adapter\Pdo4d
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 * @see https://github.com/timurgen/pecl-pdo-4d
 * @see http://sources.4d.com/trac/4d_pdo4d/raw-attachment/wiki/PDOBuilds/pdo_4d_0.3_debian.zip
 *
 * ports dispos 19812,19813,19814
 * returns 4d Pdo instance
 */
namespace Pimvc\Db\Adapter;

use Pimvc\Db\Adapter\Interfaces\Adapter as IAdapter;

class Pdo4d implements IAdapter
{

    const PREFIX_4D = '4D:';
    const PORT_PREFIX = ';port=';
    const CHARSET_PREFIX = ';charset=';
    const DEFAULT_CHARSET = 'UTF-16-LE';

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
     */
    private static function setDsn()
    {
        $dbname = self::hasValue('dbname') ? self::dbNamePrefix . self::$params['dbname'] : '';
        $port = self::hasValue(self::_PORT) ? self::PORT_PREFIX . self::$params[self::_PORT] : '';
        //$charset = self::hasValue('charset') ? self::CHARSET_PREFIX . self::$params['charset'] : '';
        $charset = self::CHARSET_PREFIX . self::DEFAULT_CHARSET;
        self::$dsn = self::PREFIX_4D
            . self::HOST_PREFIX . self::$params[self::_HOST] . ''
            . $port . $dbname; // . $charset;
        //echo self::$dsn;die;
    }

    /**
     * getOptions
     *
     * @return array
     */
    private static function getOptions()
    {
        return array(
            \PDO::ATTR_PERSISTENT => false
            , \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
            , \PDO::ERRMODE_EXCEPTION => true
            , \PDO::ATTR_CASE => \PDO::CASE_LOWER
            //, \PDO::ATTR_CASE => \PDO::CASE_NATURAL
            , \PDO::ATTR_EMULATE_PREPARES => false
            
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
