<?php

/**
 * Model_Mysql_Constraints
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */

namespace Pimvc\Model\Mysql;

class Model_Mysql_Constraints extends \Pimvc\Db\Model\Orm {
    
    protected $_schema = 'information_schema';
    protected $_name = 'table_constraints';
    protected $_primary = 'table_name';
    protected $_adapter = 'PdoMysql';
    protected $_domainSuffix = 'Mysql_';

    /**
     * @see __construct
     * 
     * @param type $config 
     */
    public function __construct($config = array()) {
        parent::__construct($config);
    }

    /**
     * getByTableName
     * 
     * @param string $tableName
     * @return array 
     */
    public function getByTableName($tableName) {
        $what = array();
        $where = array($this->getPrimary() => $tableName);
        $this->find($what, $where);
        return $this->getRowsetAsArray();
    }
    
    /**
     * getBySchema
     * 
     * @param string $schema
     * @return array 
     */
    public function getBySchema($schema) {
        $what = array();
        $where = array('table_schema' => $schema);
        $this->find($what, $where);
        return $this->getRowsetAsArray();
    }
}

