<?php
/**
 * Pimvc\Model\Mysql\Columns
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */
namespace Pimvc\Model\Mysql;

class Columns extends \Pimvc\Db\Model\Orm
{

    protected $_schema = 'information_schema';
    protected $_name = 'columns';
    protected $_primary = 'column_name';
    protected $_adapter = 'PdoMysql';
    protected $_slot = 'db0';

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
    public function getByTableId($tableId)
    {
        $what = array('*');
        $where = array('table_id' => $tableId);
        $this->find($what, $where);
        return $this->getRowsetAsArray();
    }
}
