<?php
/**
 * Pimvc\Model\Fourd\Constraints
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */
namespace Pimvc\Model\Fourd;

use \Pimvc\Db\Model\Orm;
use \Pimvc\Model\Fourd\IFourd;

class Constraints extends Orm implements IFourd
{

    protected $_name = '_USER_CONSTRAINTS';
    protected $_primary = 'constraint_id';
    protected $_adapter = Orm::MODEL_ADAPTER_4D;
    protected $_schema = '';
    protected $_slot = 'db30';
    protected $_isSystem = true;

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
}
