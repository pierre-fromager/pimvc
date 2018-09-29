<?php
/**
 * Pimvc\Model\Pgsql\Tables
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */
namespace Pimvc\Model\Pgsql;

class Columns extends \Pimvc\Db\Model\Orm
{

    const _TABLE_NAME = 'table_name';

    protected $_schema = 'information_schema';
    protected $_name = 'columns';
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
    public function get()
    {
        $this->find();
        return $this->getRowsetAsArray();
    }

    /**
     * getByTableName
     *
     * @param string $tableName
     * @return array
     */
    public function getByTableName($tableName)
    {
        $what = [
            'ordinal_position',
            'column_name',
            'data_type',
            'column_default',
            'is_nullable',
            'character_maximum_length',
            'numeric_precision'
        ];
        $this->find($what, [self::_TABLE_NAME => $tableName]);
        return $this->getRowsetAsArray();
    }
}
