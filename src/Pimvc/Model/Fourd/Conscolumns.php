<?php
/**
 * Pimvc\Model\Fourd\Conscolumns
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */
namespace Pimvc\Model\Fourd;

class Conscolumns extends \Pimvc\Db\Model\Orm
{

    protected $_name = '_USER_CONS_COLUMNS';
    protected $_primary = 'constraint_id';
    protected $_adapter = 'Pdo4d';
    protected $_mapperSuffix = '4d_';

    /**
     * @see __construct
     *
     * @param array $config
     */
    public function __construct($config = array())
    {
        parent::__construct($config);
    }

    /**
     * getByTablename
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

    /**
     * getByColumnName
     *
     * @param string $columnName
     * @return array
     */
    public function getByColumnName($columnName)
    {
        $what = array('*');
        $where = array('column_name' => $columnName);
        $this->find($what, $where);
        return $this->getRowsetAsArray();
    }

    /**
     * getByColumnId
     *
     * @param int $columnId
     * @return array
     */
    public function getByColumnId($columnId)
    {
        $what = array('*');
        $where = array('column_id' => $columnId);
        $this->find($what, $where);
        return $this->getRowsetAsArray();
    }
}
