<?php
/**
 * Description of Pimvc\Db\Model\Forge
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */
namespace Pimvc\Db\Model;

use Pimvc\Db\Model\Exceptions\Orm as ormException;
use Pimvc\Db\Model\Core as dbCore;

class Forge extends dbCore implements Interfaces\Forge
{

    protected $db;
    protected $dbConfig;

    /**
     * __construct
     *
     * @param string $slot
     */
    public function __construct($slot = '')
    {
        $this->setDbConfig();
        if ($slot) {
            $this->setDb($slot);
        }
    }

    /**
     * setDb
     *
     * @param string $slot
     * @return $this
     * @throws ormException
     */
    public function setDb($slot)
    {
        if (!isset($this->dbConfig[$slot])) {
            throw new ormException(ormException::ORM_EXC_MISSING_SLOT);
        }
        if (!isset($this->dbConfig[$slot][self::_ADAPTER])) {
            throw new ormException(ormException::ORM_EXC_MISSING_ADAPTER);
        }
        $this->db = \Pimvc\Db\Factory::getConnection($this->dbConfig[$slot]);
        return $this;
    }

    public function createTable($tableName, $fieldsProperties)
    {
        $sql = "CREATE TABLE $tableName (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
firstname VARCHAR(30) NOT NULL,
lastname VARCHAR(30) NOT NULL,
email VARCHAR(50),
reg_date TIMESTAMP
)";
        $this->run($sql, $bindParams);
    }

    /**
     * setDbConfig
     *
     */
    protected function setDbConfig()
    {
        $this->dbConfig = \Pimvc\App::getInstance()->getConfig()[self::_DB_POOL];
    }
}
