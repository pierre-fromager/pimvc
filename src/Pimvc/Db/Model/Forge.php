<?php
/**
 * Description of Pimvc\Db\Model\Forge
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */
namespace Pimvc\Db\Model;

use Pimvc\Db\Model\Exceptions\Orm as ormException;
use Pimvc\Db\Model\Core as dbCore;
use Pimvc\Db\Model\Field as dbField;
use Pimvc\Db\Model\Fields as dbFields;

class Forge extends dbCore implements Interfaces\Forge
{

    protected $dbConfig;

    /**
     * __construct
     *
     * @param string $slot
     */
    public function __construct(string $slot = '')
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
     * @param Pimvc\Db\Model\Fields $columns
     * @param bool $withPk
     */
    public function tableCreate(string $tableName, \Pimvc\Db\Model\Fields $columns, bool $withPk = true)
    {
        $countColumns = count($columns);
        for ($c = 0; $c < $countColumns; $c++) {
            $field = $columns[$c];
            $typeLength = $this->getTypeFromField($field);
            $typeLength .= $this->getParentheses($field->getMaxLen());
            $fields[] = $field->getName() . ' ' . $typeLength;
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
     * tableInsert
     *
     * @param string $tablename
     * @param array $headers
     * @param array $datas
     */
    public function tableInsert(string $tablename, array $headers, array $datas): bool
    {
        if (count($headers) === count($datas)) {
            $types = $this->getPdoTypes($tablename);
            $bindFields = array_map(function ($v) {
                return ':' . $v;
            }, $headers);
            $statementBindings = array_combine($headers, $datas);
            $bindTypes = array_map(function ($v) use ($types) {
                return $types[$v];
            }, $headers);
            $fields = $this->getParentheses($this->build($headers, ','));
            $values = $this->getParentheses($this->build($bindFields, ','));
            $sql = $this->build(
                [$this->insertInto($tablename), $fields, self::_VALUES, $values]
            );
            $this->run($sql, $statementBindings, $bindTypes);
            return true;
        }
        return false;
    }

    /**
     * getPdoTypes
     *
     * @param string $tablename
     * @return array
     */
    public function getPdoTypes(string $tablename): array
    {
        $fiedsDesc = $this->describeTable($tablename);
        $types = [];
        foreach ($fiedsDesc as $fieldDesc) {
            $rawType = $fieldDesc['type'];
            $regex = '(varchar|text|float)';
            $fieldName = $fieldDesc['field'];
            $types[$fieldName] = (preg_match("/^$regex$/", $rawType)) ? \PDO::PARAM_STR : \PDO::PARAM_INT;
        }
        return $types;
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
     * insertInto
     *
     * @param string $name
     */
    private function insertInto($name = '')
    {
        $insert = [self::_INSERT, self::_INTO];
        if ($name) {
            $insert[] = $name;
        }
        return $insert;
    }

    /**
     * createTable
     *
     * @param string $name
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
    private function getTypeFromField(\Pimvc\Db\Model\Field $field)
    {
        $type = ($field->getIsString()) ? self::_VARCHAR : self::_INT;
        $type = ($field->getIsFloat()) ? self::_FLOAT : $type;
        return $type;
    }

    /**
     * getParentheses
     * @param string $value
     * @return string
     */
    private function getParentheses(string $value): string
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
        $slotDbConfig = $this->dbConfig[$slot];
        $this->_adapter = $slotDbConfig['adapter'];
        $this->_db = \Pimvc\Db\Factory::getConnection($slotDbConfig);
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

    /**
     * statementErrorPrepareChecking
     *
     */
    protected function statementErrorPrepareChecking($sql)
    {
        
        if ($this->_statement === false) {
            if ($this->_restMode) {
                $this->_errorCode = 5000;
                $this->_errorMessage = 'Statement prepare error';
            } else {
                echo '<p style="color:red;">Error prepare sql : '
                . $sql
                . '</p>';
                die;
            }
        }
    }

    /**
     * statementErrorBind
     *
     * @param \PDOException $exc
     */
    protected function statementErrorBind(\PDOException $exc, $queryType)
    {
        $this->_error = $this->_errorMessage = $exc->getMessage();
        $this->_errorCode = $exc->getCode();
        $this->_logger->logError(
            'Sql Bind Error' . $queryType . ' ' . $exc->getMessage(),
            $this->_statement->queryString
        );
    }

    /**
     * statementErrorExecute
     *
     * @param \PDOException $exc
     */
    protected function statementErrorExecute(\PDOException $exc, $queryType)
    {
        $this->_error = $exc->getMessage();
        $this->_errorCode = $exc->getCode();
        $this->_errorMessage = $exc->getMessage();
        $this->_logger->logError(
            'Sql Execute Failed ' . $queryType . ' ' . $exc->getMessage(),
            $this->_statement->queryString
        );
        $isExecError = (self::MODEL_DEBUG && !$this->_restMode);
    }
}
