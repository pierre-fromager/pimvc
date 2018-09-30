<?php
/**
 * Pimvc\Model\Fourd\Indexes
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 * @todo https://www.alberton.info/postgresql_meta_info.html
 */
namespace Pimvc\Model\Pgsql;

class Indexes extends \Pimvc\Db\Model\Orm
{

    protected $_name = 'pg_index';
    protected $_primary = 'indexrelid';
    protected $_adapter = 'PdoPgsql';
    protected $_slot = 'db10';

    /**
     * @see __construct
     *
     * @param array $config
     */
    public function __construct($config = [])
    {
        parent::__construct($config);
    }

    /**
     * getByTablename
     *
     * @param string $tableName
     * @return array
     */
    public function getByTablename($tableName)
    {
        $this->find([], ['table_name' => $tableName]);
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
        $this->find([], ['table_id' => $tableId]);
        return $this->getRowsetAsArray();
    }
}
