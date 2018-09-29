<?php
/**
 * Pimvc\Model\Fourd\Schemas
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */
namespace Pimvc\Model\Fourd;

class Schemas extends \Pimvc\Db\Model\Orm
{

    protected $_name = '_USER_SCHEMAS';
    protected $_primary = 'schema_id';
    protected $_adapter = 'Pdo4d';
    protected $_mapperSuffix = '4d_';

    public function __construct($config = array())
    {
        parent::__construct($config);
    }

    public function get()
    {
        $this->find();
        return $this->getRowsetAsArray();
    }
}
