<?php
/**
 * Pimvc\Model\Fourd\Columns
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */
namespace Pimvc\Model\Fourd;

use \Pimvc\Db\Model\Orm;
use \Pimvc\Model\Fourd\IFourd;

class Columns extends Orm implements IFourd
{

    protected $_name = '_USER_COLUMNS';
    protected $_primary = 'column_id';
    protected $_adapter = Orm::MODEL_ADAPTER_4D;
    protected $_schema = '';
    protected $_slot = 'db30';

    /**
     * @see __construct
     *
     * @param type $config
     */
    public function __construct($config = array())
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
}
