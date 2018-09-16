<?php

/**
 * Model_Mysql_Constraints
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */

namespace Pimvc\Model\Mysql;

class Constraints extends \Pimvc\Db\Model\Orm
{
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
    public function __construct($config = [])
    {
        parent::__construct($config);
    }

    /**
     * getByTableName
     *
     * @param string $tableName
     * @return array
     */
    public function getByTableName($tableName)
    {
        $what = [];
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
    public function getBySchema($schema)
    {
        $what = [];
        $where = array('table_schema' => $schema);
        $this->find($what, $where);
        return $this->getRowsetAsArray();
    }
}
