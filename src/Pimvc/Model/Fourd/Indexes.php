<?php
/**
 * Pimvc\Model\Fourd\Indexes
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */
namespace Pimvc\Model\Fourd;

use \Pimvc\Db\Model\Orm;
use \Pimvc\Model\Fourd\IFourd;

class Indexes extends Orm implements IFourd
{

    protected $_name = '_USER_INDEXES';
    protected $_primary = 'index_id';
    protected $_adapter = Orm::MODEL_ADAPTER_4D;
    protected $_schema = '';
    protected $_slot = 'db30';
    protected $_isSystem = true;

    /**
     * @see __construct
     *
     * @param array $config
     */
    public function __construct($config = array())
    {
        $this->_adapter = Orm::MODEL_ADAPTER_4D;
        parent::__construct($config);
        return $this;
    }

    /**
     * getByTablename
     *
     * @param string $tableName
     * @return array
     */
    public function getByTablename($tableName)
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
