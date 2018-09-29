<?php
/**
 * Pimvc\Model\Pgsql\Tables
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */
namespace Pimvc\Model\Pgsql;

class Tables extends \Pimvc\Db\Model\Orm
{

    protected $_name = 'pg_catalog.pg_tables';
    protected $_primary = 'tablename';
    protected $_adapter = 'PdoPgsql';
    protected $_slot = 'db10';

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
        $this->find(array(), array(), array($this->_primary => 'asc'));
        $tablesInfos = $this->getRowsetAsArray();
        foreach ($tablesInfos as $tablesInfo) {
            $id = $tablesInfo['tablename'];
            $name = $tablesInfo['tablename'];
            $tables[$name] = $id;
        }
        return $tables;
    }
}
