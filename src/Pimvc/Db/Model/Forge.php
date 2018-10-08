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

    protected $dbConfig;

    /**
     * __construct
     *
     * @param string $slot
     */
    public function __construct($slot = '')
    {
        $this->setLogger();
        $this->setDbConfig();
        if ($slot) {
            try {
                $this->setDb($slot);
            } catch (ormException $exc) {
                echo $exc->getTraceAsString();
            }
        }
    }

    /**
     * tableCreate
     * 
     * @param string $tableName
     * @param array $columns
     * @param boolean $withPk
     */
    public function tableCreate($tableName, $columns, $withPk = true)
    {
        $fields = [];
        foreach ($columns as $field) {
            $typeLength = $this->getTypeFromField($field);
            $typeLength .= $this->getParentheses($field->maxLength);
            $fields[] = $field->name . ' ' . $typeLength;
        }
        $cs = ' ,';
        $optionalFields = [];
        if ($withPk) {
            $optionalFields[] = $this->build(
                $this->pk('id', self::_INT, 6)
            );
        }
        $sqlFields = $this->build([$optionalFields, $this->build($fields, $cs)], $cs);
        $sql = $this->build([
            $this->createTable($tableName),
            $this->getParentheses($sqlFields)
        ]);
        $this->run($sql);
    }

    /**
     * renameTable
     *
     * @param string $name
     * @param string $newName
     */
    public function tableRename($name, $newName)
    {
        $this->run(
            $this->build(
                [$this->alterTable($name), $this->renameTo($newName)]
            )
        );
    }

    /**
     * tableCreate
     *
     * @param string $name
     * @param string $newName
     */
    private function createTable($name = '')
    {
        $create = [self::_CREATE, self::_TABLE];
        if ($name) {
            $create[] = $name;
        }
        return $create;
    }

    /**
     * pk
     *
     * @param string $name
     * @param string $type
     * @param int $size
     * @return array
     */
    private function pk($name, $type, $size)
    {
        return [
            $name,
            $type,
            $this->getParentheses($size) . ' ' . self::_UNSIGNED,
            self::_AUTO_INCREMENT,
            self::_PRIMARY_KEY
        ];
    }

    /**
     * getTypeFromField
     *
     * @param Field $field
     * @return string
     */
    private function getTypeFromField($field)
    {
        $type = ($field->isString) ? self::_VARCHAR : self::_INT;
        $type = ($field->isFloat) ? self::_FLOAT : $type;
        return $type;
    }

    /**
     * getParentheses
     * 
     * @param string $value
     * @return string
     */
    private function getParentheses($value)
    {
        return '(' . $value . ')';
    }

    /**
     * alterTable
     *
     * @param string $name
     * @return array
     */
    private function alterTable($name = '')
    {
        $alter = [self::_ALTER, self::_TABLE];
        if ($name) {
            $alter[] = $name;
        }
        return $alter;
    }

    /**
     * renameTo
     *
     * @param type $name
     * @return type
     */
    private function renameTo($name = '')
    {
        $rename = [self::_RENAME, self::_TO];
        if ($name) {
            $rename[] = $name;
        }
        return $rename;
    }

    /**
     * setDbConfig
     *
     */
    protected function setDbConfig()
    {
        $this->dbConfig = \Pimvc\App::getInstance()->getConfig()->getSettings(
            self::_DB_POOL
        );
    }

    /**
     * setDb
     *
     * @param string $slot
     * @return $this
     * @throws ormException
     */
    protected function setDb($slot)
    {
        if (!isset($this->dbConfig[$slot])) {
            throw new ormException(ormException::ORM_EXC_MISSING_SLOT);
        }
        if (!isset($this->dbConfig[$slot][self::_ADAPTER])) {
            throw new ormException(ormException::ORM_EXC_MISSING_ADAPTER);
        }
        $this->_db = \Pimvc\Db\Factory::getConnection($this->dbConfig[$slot]);
        return $this;
    }

    /**
     * setLogger
     *
     */
    protected function setLogger()
    {
        $app = \Pimvc\App::getInstance();
        $this->_logger = \Pimvc\Logger::getFileInstance(
            $app->getPath() . '/log/',
            \Pimvc\Logger::DEBUG,
            \Pimvc\Logger::LOG_ADAPTER_FILE
        );
    }

    /**
     * build
     *
     * @param array $verbs
     * @return string
     */
    private function build($verbs, $glue = ' ')
    {
        $parts = [];
        foreach ($verbs as $verb) {
            if (is_array($verb)) {
                if (!empty($verb)) {
                    $parts[] = implode($glue, $verb);
                }
            } else {
                $parts[] = $verb;
            }
        }
        return implode($glue, $parts);
    }
}
