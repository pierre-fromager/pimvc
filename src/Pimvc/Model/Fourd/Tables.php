<?php
/**
 * Pimvc\Model\Fourd\Tables
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */
namespace Pimvc\Model\Fourd;

use \Pimvc\Db\Model\Orm;
use \Pimvc\Model\Fourd\IFourd;

class Tables extends Orm implements IFourd
{

    protected $_name = '_USER_TABLES';
    protected $_primary = 'table_id';
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
     * get
     *
     * @return array
     */
    public function get()
    {
        return $this->find()->getRowsetAsArray();
    }

    /**
     * getPair
     *
     * @return array
     */
    public function getPair()
    {
        $tables = [];
        $this->find([], [], [self::_TABLE_NAME => 'asc']);
        $tablesInfos = $this->getRowsetAsArray();
        foreach ($tablesInfos as $tablesInfo) {
            $id = $tablesInfo[$this->_primary];
            $name = $tablesInfo[self::_TABLE_NAME];
            $tables[$name] = $id;
        }
        return $tables;
    }
}
