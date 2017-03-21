<?php

/**
 * pdosqlite returns Sqlite2 Pdo instance
 * @author Pierre Fromager <pf@pier-infor.fr>
 */

namespace Pimvc\Db\Adapter;

class Pdosqlite {

    const prefix = 'sqlite:';
    const errorConnectFailed = 'Connexion échouée : ';

    protected static $dsn = null;
    protected static $params = null;
    protected static $_instance = null;

    private function __construct() {}

    private function __clone() {}

    /**
     * setDsn : returns dsn string
     * @param array $params
     */
    private static function setDsn() {
        self::$dsn = self::prefix . self::$params['file'];
    }

    /**
     * getInstance : returns Mysql Pdo Instance
     * @param array $params
     */
    public static function getInstance($params) {
        self::$params = $params;
        self::setDsn();
        if (self::$_instance === null) {
            try {
                self::$_instance = new PDO(self::$dsn);
            } catch (PDOException $e) {
                echo self::errorConnectFailed . $e->getMessage();
            }
        }
        return self::$_instance;
    }

}
