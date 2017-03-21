<?php

/**
 * Class Db is a factory instanciates a Db connection 
 * for a given Dsn and Adpater
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 * 
 */

namespace Pimvc\Db;

class Factory {

    const ADAPTER_CLASS_PREFIX = '\Pimvc\Db\Adapter\\';
    const FACTORY_ADAPTER = 'adapter';
    const FACTORY_NAME = 'name';
    const FACTORY_SEP = '-';

    protected static $_connections = array();

    private function __construct() {}
    private function __clone() {}

    /**
     * getConnection tries to instanciate Db with Adapter
     * @param array $params
     * @throws Exception
     * @return mixed
     */
    public static function getConnection($params) {
        $adapter = $params[self::FACTORY_ADAPTER];
        $instanceName = $adapter . self::FACTORY_SEP . $params[self::FACTORY_NAME];
        if (!isset(self::$_connections[$instanceName])) {
            $adapterClassName = self::ADAPTER_CLASS_PREFIX . ucfirst(strtolower($adapter));
            try {
                self::$_connections[$instanceName] = $adapterClassName::getInstance($params);
            } catch (PDOException $e) {
                throw $e->getMessage();
            }
        }
        return self::$_connections[$instanceName];
    }
    
}