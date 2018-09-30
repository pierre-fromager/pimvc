<?php
/**
 * Pimvc\Model\Pgsql\Pclass
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 * @todo https://www.alberton.info/postgresql_meta_info.html
 */
namespace Pimvc\Model\Pgsql;

class Pclass extends \Pimvc\Db\Model\Orm
{

    const _TABLE_NAME = 'table_name';

    protected $_schema = 'information_schema';
    protected $_name = 'pg_class';
    protected $_primary = 'column_name';
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
    public function getAll()
    {
        return $this->find()->getRowsetAsArray();
    }

    /**
     * indexes
     * 
     * @param string $tableName
     * @return array
     */
    public function indexes($tableName)
    {
        //echo $this->join('pg_index', 'indrelid', $lt, 'oid')
        $sql = 'SELECT c.relname AS index_name FROM pg_class AS a ' .
            'JOIN pg_index AS b ON (a.oid = b.indrelid) ' .
            'JOIN pg_class AS c ON (c.oid = b.indexrelid) ' .
            " WHERE a.relname = '$tableName';";
        $this->run($sql);
        $res = $this->_statement->fetchAll();
        $indexRes = array_map(
            function($v) {
            return $v['index_name'];
        }, $res);

        return $indexRes;
    }
}
