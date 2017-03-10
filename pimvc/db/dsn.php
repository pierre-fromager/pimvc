<?php

/**
 * Lib_Db_Dsn
 * 
 * Class management to build dsn for a given adapter or slot
 */

namespace pimvc\db;

class dsn {

    const PARAM_CONFIG = 'Config';
    const ADAPTER_PDO_MYSQL = 'PdoMysql';
    const ADAPTER_PDO_PGSQL = 'PdoPgsql';
    const ADAPTER_PDO_4D = 'Pdo4d';
    const ADAPTER_PDO_SQLITE = 'PdoSqlite';
    const SLOT_MAX = 5;
    const SLOT_PREFIX = 'db';
    const ERROR_MESSAGE_1 = 'Invalid slot (';
    const ERROR_MESSAGE_2 = ') adapter (';
    const ERROR_MESSAGE_3 = ') !';

    public $dsn = null;
    protected $dsnOption = null;
    protected $appConf = null;
    protected $adapter = null;
    protected $slot = null;

    /**
     * __construct
     * 
     * @param string $adapter
     * @param string $slot
     */
    public function __construct($config, $slot) {
        //echo print_r($config, true);
        //die;
        $adapterReady = (!empty($adapter)) && in_array($adapter, $this->getAllowedAdapters());
        $this->adapter = $adapter;
        $this->slot = $slot;
        $this->appConf = $config;
        if ($adapterReady && $this->slotExists()) {
            $this->dsn = $this->build();
        } else {
            throw new \Exception('Unknown slot');
        }
        if (!$this->dsn) {
            $exceptionErrorMessage = self::ERROR_MESSAGE_1 . $this->slot
                    . self::ERROR_MESSAGE_2 . $this->adapter
                    . self::ERROR_MESSAGE_3;
            throw new \Exception($exceptionErrorMessage);
        }
    }
    
    

    /**
     * setDbname
     * 
     * @param string $value 
     */
    public function setSchema($value) {
        $this->set('dbname', $value);
    }

    /**
     * getSchema
     * 
     * @return string 
     */
    public function getSchema() {
        return $this->dsn['dbname'];
    }

    /**
     * get
     * 
     * @return array 
     */
    public function get() {
        return $this->dsn;
    }

    /**
     * set
     * 
     * @param string $param
     * @param string $value 
     */
    private function set($param, $value) {
        $this->dsn[$param] = $value;
    }

    /**
     * getAllowedAdapters
     * 
     * @return type 
     */
    private function getAllowedAdapters() {
        return array(
            self::ADAPTER_PDO_4D
            , self::ADAPTER_PDO_MYSQL
            , self::ADAPTER_PDO_PGSQL
            , self::ADAPTER_PDO_SQLITE
        );
    }

    /**
     * adapterExists
     * 
     * @return boolean 
     */
    private function adapterExists() {
        return in_array($this->adapter, $this->getAllowedAdapters());
    }

    /**
     * slotExists
     * 
     * @return boolean 
     */
    private function slotExists() {

        return ($this->slot) ? isset($this->appConf->{$this->slot}) : true;
    }

    /**
     * build
     * 
     * @return type 
     */
    private function build() {
        if ($this->slotExists() && $this->adapterExists()) {
            switch ($this->adapter) {
                case self::ADAPTER_PDO_MYSQL:
                    $slotName = ($this->slot) ? $this->slot : 'db';
                    $dsnOptions = array(
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
                        , PDO::ATTR_PERSISTENT => false
                        , PDO::ATTR_ERRMODE => true
                        , PDO::ERRMODE_EXCEPTION => false
                    );
                    $dsn = array(
                        'adapter' => $this->appConf->{$slotName}->adapter
                        , 'dbname' => $this->appConf->{$slotName}->name
                        , 'host' => $this->appConf->{$slotName}->host
                        , 'user' => $this->appConf->{$slotName}->user
                        , 'password' => $this->appConf->{$slotName}->password
                        , 'options' => $dsnOptions
                    );
                    break;
                case self::ADAPTER_PDO_SQLITE:
                    $slotName = ($this->slot) ? $this->slot : 'db2';
                    $dsn = array(
                        'adapter' => $this->appConf->{$slotName}->adapter
                        , 'dbname' => $this->appConf->{$slotName}->name
                    );
                    break;
                case self::ADAPTER_PDO_PGSQL:
                    $slotName = ($this->slot) ? $this->slot : 'db3';
                    $dsn = array(
                        'adapter' => $this->appConf->{$slotName}->adapter
                        , 'dbname' => $this->appConf->{$slotName}->name
                        , 'host' => $this->appConf->{$slotName}->host
                        , 'user' => $this->appConf->{$slotName}->user
                        , 'password' => $this->appConf->{$slotName}->password
                            //,'options'=>$dsnOptions
                    );
                    break;
                case self::ADAPTER_PDO_4D:
                    $slotName = ($this->slot) ? $this->slot : 'db4';
                    $dsn = array(
                        'adapter' => $this->appConf->{$slotName}->adapter
                        , 'dbname' => $this->appConf->{$slotName}->name
                        , 'host' => $this->appConf->{$slotName}->host
                        , 'user' => $this->appConf->{$slotName}->user
                        , 'password' => $this->appConf->{$slotName}->password
                            //,'options'=>$dsnOptions
                    );
                    break;
            }
            return $dsn;
        }
    }

}
