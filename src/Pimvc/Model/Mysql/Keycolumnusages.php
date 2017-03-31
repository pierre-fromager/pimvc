<?php

/**
 * Model_Mysql_Keycolumnusages
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */

namespace Pimvc\Model\Mysql;

class Model_Mysql_Keycolumnusages extends \Pimvc\Db\Model\Orm {

    protected $_schema = 'information_schema';
    protected $_name = 'key_column_usage';
    protected $_primary = 'column_name';
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
        $defaultSchema = $this->getDefaultSchema();
        $where = array(
            'table_name' => $tableName
            ,'constraint_schema' => $defaultSchema
            , 'table_schema' => $defaultSchema         
        );
        $this->find($what, $where);
        return $this->getRowsetAsArray();
    }
    
    /**
     * getBySchema
     * 
     * @param int $tableId
     * @return array 
     */
    public function getBySchema($schema) {
        $what = array();
        $where = array(
            'constraint_schema' => $schema
            , 'table_schema' => $schema
            , 'referenced_table_schema' => $schema
        );
        $order = array('table_name' => 'ASC');
        $this->find($what, $where, $order);
        return $this->getRowsetAsArray();
    }
    
}

