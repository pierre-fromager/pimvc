<?php

/**
 * Model_Mysql_Columns
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */
namespace Pimvc\Model\Mysql;

class Columns extends \Pimvc\Db\Model\Orm {

    protected $_schema = 'INFORMATION_SCHEMA';
    protected $_name = 'COLUMNS';
    protected $_primary = 'COLUMN_NAME';
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
        $what = array('*');
        $where = array('table_name' => $tableName);
        $this->find($what, $where);
        return $this->getRowsetAsArray();
    }
    
    /**
     * getByTableId
     * 
     * @param int $tableId
     * @return array 
     */
    public function getByTableId($tableId) {
        $what = array('*');
        $where = array('table_id' => $tableId);
        $this->find($what, $where);
        return $this->getRowsetAsArray();
    }
    
}

