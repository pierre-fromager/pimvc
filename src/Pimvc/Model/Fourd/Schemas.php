<?php
/**
 * Pimvc\Model\Fourd\Schemas
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */
namespace Pimvc\Model\Fourd;

use \Pimvc\Db\Model\Orm;
use \Pimvc\Model\Fourd\IFourd;

class Schemas extends Orm implements IFourd
{

    protected $_name = '_USER_SCHEMAS';
    protected $_primary = 'schema_id';
    protected $_adapter = Orm::MODEL_ADAPTER_4D;
    protected $_schema = '';
    protected $_slot = 'db30';
    protected $_isSystem = true;

    /**
     * __construct
     *
     * @param array $config
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
}
