<?php
/**
 * Pimvc\Model\Pgsql\Tables
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 * @todo https://www.alberton.info/postgresql_meta_info.html
 */
namespace Pimvc\Model\Pgsql;

class Tables extends \Pimvc\Db\Model\Orm
{

    const _TABLENAME = 'tablename';

    protected $_name = 'pg_catalog.pg_tables';
    protected $_primary = self::_TABLENAME;
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
        $this->find([], [], [$this->_primary => self::MODEL_ORDER_ASC]);
        $tablesInfos = $this->getRowsetAsArray();
        foreach ($tablesInfos as $tablesInfo) {
            $id = $tablesInfo[self::_TABLENAME];
            $name = $tablesInfo[self::_TABLENAME];
            $tables[$name] = $id;
        }
        return $tables;
    }
}
