<?php
/**
 * Pimvc\Model\Fourd\Tables
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */
namespace Pimvc\Model\Fourd;

class Tables extends \Pimvc\Db\Model\Orm
{

    protected $_name = '_USER_TABLES';
    protected $_primary = 'table_id';
    protected $_adapter = 'Pdo4d';
    protected $_mapperSuffix = '4d_';

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
        $this->find();
        return $this->getRowsetAsArray();
    }

    /**
     * getPair
     *
     * @return array
     */
    public function getPair()
    {
        $tables = array();
        $this->find(array(), array(), array('table_name' => 'asc'));
        $tablesInfos = $this->getRowsetAsArray();
        foreach ($tablesInfos as $tablesInfo) {
            $id = $tablesInfo['table_id'];
            $name = $tablesInfo['table_name'];
            $tables[$name] = $id;
        }
        return $tables;
    }
}
