<?php

/**
 * Class Db is a factory instanciates a Db connection 
 * for a given Dsn and Adpater
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 * 
 */

namespace lib\db;

class factory {

    const ADAPTER_PATH = '/Adapter/';

    protected static $_connections = array();

    /*
     * Class Constructor - Create a new database connection if one doesn't exist
     * Set to private so no-one can create a new instance via ' = new DB();'
     */

    private function __construct() {
        
    }

    /*
     * Like the constructor, we make __clone private so nobody can clone the instance
     */

    private function __clone() {
        
    }

    /**
     * getConnection tries to instanciate Db with Adapter
     * @param array $params
     * @throws Exception
     * @return mixed
     */
    public static function getConnection($params) {
        $adapter = $params['adapter'];
        $instanceName = $adapter . '-' . $params['name'];
        if (!isset(self::$_connections[$instanceName])) {
            $adapterClassName = '\lib\db\adapter\\' . strtolower($adapter);
            /*
            echo $adapterClassName;die;
            $adapterName = 'Db' . ucfirst(strtolower($adapter)) . 'Adapter';
            $adapterFile = dirname(__FILE__) . self::ADAPTER_PATH . $adapterName . '.php';
            if (file_exists($adapterFile))
                require_once $adapterFile;
            else
                throw new Exception($adapterFile . ' not found.');
            if (!class_exists($adapterName)) {
                die(sprintf('Adapter "%s" (%s) is not installed ', $adapter, $adapterName));
            }*/
            try {
                self::$_connections[$instanceName] = $adapterClassName::getInstance($params);
            } catch (PDOException $e) {
                throw $e->getMessage();
            }
        }
        return self::$_connections[$instanceName];
    }
    
    private static function setPoolInstance() {
        
    }

    /*
     * Passes on any static calls to this class onto the singleton PDO instance
     * @param $chrMethod, $arrArguments
     * @return $mix
     */
    /*
      final public static function __callStatic( $chrMethod, $arrArguments ) {

      $objInstance = self::getInstance();

      return call_user_func_array(array($objInstance, $chrMethod), $arrArguments);

      } # end method */
}

?>