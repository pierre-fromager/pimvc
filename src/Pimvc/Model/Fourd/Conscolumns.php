<?php
/**
 * Pimvc\Model\Fourd\Conscolumns
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */
namespace Pimvc\Model\Fourd;

use \Pimvc\Db\Model\Orm;
use \Pimvc\Model\Fourd\IFourd;

class Conscolumns extends Orm implements IFourd
{

    protected $_name = '_USER_CONS_COLUMNS';
    protected $_primary = 'constraint_id';
    protected $_adapter = Orm::MODEL_ADAPTER_4D;
    protected $_schema = '';
    protected $_slot = 'db30';

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
        return $this->find(
            [],
            [self::_TABLE_NAME => $tableName]
        )->getRowsetAsArray();
    }

    /**
     * getByTableId
     *
     * @param int $tableId
     * @return array
     */
    public function getByTableId($tableId)
    {
        return $this->find(
            [],
            [self::_TABLE_ID => (int) $tableId]
        )->getRowsetAsArray();
    }

    /**
     * getByColumnName
     *
     * @param string $columnName
     * @return array
     */
    public function getByColumnName($columnName)
    {
        return $this->find(
            [],
            [self::_COLUM_NAME => $columnName]
        )->getRowsetAsArray();
    }

    /**
     * getByColumnId
     *
     * @param int $columnId
     * @return array
     */
    public function getByColumnId($columnId)
    {
        return $this->find(
            [],
            [self::_COLUM_ID => (int) $columnId]
        )->getRowsetAsArray();
    }
}
