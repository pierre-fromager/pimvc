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
    protected $slot;
    protected $_adapter;

    /**
     * __construct
     *
     * @param string $slot
     */
    public function __construct(string $slot = '')
    {
        $this->slot = $slot;
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
                $this->getColumnDesc('id', self::_INT, 6, false, true, true)
            );
        }
        $sqlFields = $this->build([$optionalFields, $this->build($fields, $cs)], $cs);
        $sql = $this->build([
            $this->createTable($tableName),
            $this->getParentheses($sqlFields)
        ]);
        switch ($this->_adapter) {
            case \Pimvc\Db\Model\Core::MODEL_ADAPTER_SQLITE:
                $sql = preg_replace('/\([0-9]+\)/', '', $sql);
                $sql = str_replace('INT', 'INTEGER', $sql);
                $sql = str_replace('VARCHAR', 'TEXT', $sql);
                $sql = str_replace('FLOAT', 'REAL', $sql);
                $sql = str_replace('UNSIGNED AUTO_INCREMENT PRIMARY KEY', 'PRIMARY KEY AUTOINCREMENT', $sql);
                break;

            case \Pimvc\Db\Model\Core::MODEL_ADAPTER_PGSQL:
                $sql = str_replace('VARCHAR', 'CHAR', $sql);
                $sql = str_replace('FLOAT', 'REAL', $sql);
                $sql = preg_replace('/INT\([0-9]+\)/', 'INT', $sql);
                $sql = preg_replace('/REAL\([0-9]+\)/', 'REAL', $sql);
                $sql = str_replace('INT UNSIGNED AUTO_INCREMENT PRIMARY KEY', 'SERIAL PRIMARY KEY', $sql);
                break;
        }
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
     * tableAddField
     *
     * @param string $tablename
     * @param string $name
     */
    public function tableAddField($tablename, $name)
    {
        $sql = $this->build(
            $this->alterTable($tablename),
            $this->addColumn($name)
        );
        $this->run($sql);
    }

    /**
     * tableSetIndex
     *
     * @param string $tablename
     * @param string $fieldname
     */
    public function tableSetIndex($tablename, $fieldname)
    {
        $tableIndex = [
            $this->alterTableAdd($tablename),
            self::_INDEX,
            $this->getParentheses($fieldname)
        ];
        $sql = $this->build($tableIndex);
        $this->run($sql);
    }

    /**
     * tableSetIndex
     *
     * @param string $tablename
     * @param string $fieldname
     */
    public function tableSetUnique($tablename, $fieldname)
    {
        $tableIndex = [
            $this->alterTableAdd($tablename),
            self::_UNIQUE,
            $this->getParentheses($fieldname)
        ];
        $sql = $this->build($tableIndex);
        $this->run($sql);
    }

    /**
     * addColumn
     *
     * @param string $name
     * @return array
     */
    private function addColumn($name = '')
    {
        $addColumn = [self::_ADD, self::_COLUMN];
        if ($name) {
            $addColumn[] = $name;
        }
        return $addColumn;
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
        $fieldNameEntry = '';
        $fieldDescTypeEntry = '';
        switch ($this->_adapter) {
            case \Pimvc\Db\Model\Core::MODEL_ADAPTER_SQLITE:
                $fieldNameEntry = 'field';
                $fieldDescTypeEntry = 'type';
                break;

            case \Pimvc\Db\Model\Core::MODEL_ADAPTER_MYSQL:
                $fieldNameEntry = 'name';
                $fieldDescTypeEntry = 'type';
                break;

            case \Pimvc\Db\Model\Core::MODEL_ADAPTER_PGSQL:
                $fieldNameEntry = 'column_name';
                $fieldDescTypeEntry = 'data_type';
                break;
        }
        $types = [];
        foreach ($fiedsDesc as $fieldDesc) {
            $rawType = $fieldDesc[$fieldDescTypeEntry];
            $regex = '/^int/';
            $fieldName = $fieldDesc[$fieldNameEntry];
            $types[$fieldName] = (preg_match($regex, $rawType)) ? \PDO::PARAM_INT : \PDO::PARAM_STR;
        }
        return $types;
    }

    /**
     * renameTable
     *
     * @param string $name
     * @param string $newName
     */
    public function tableRename(string $name, string $newName)
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
     * getColumnDesc
     *
     * @param string $name
     * @param string $type
     * @param int $size
     * @param bool $signed
     * @param bool $autoInc
     * @param bool $pkey
     * @return array
     */
    private function getColumnDesc($name, $type, $size, $signed = false, $autoInc = false, $pkey = false)
    {
        $columnDesc = [
            $name,
            $type . $this->getParentheses($size)
        ];
        if (!in_array($type, [self::_VARCHAR, 'TEXT'])) {
            $columnDesc[] = (!$signed) ? self::_UNSIGNED : '';
            if ($autoInc) {
                $columnDesc[] = self::_AUTO_INCREMENT;
            }
        }
        if ($pkey) {
            $columnDesc[] = self::_PRIMARY_KEY;
        }

        return $columnDesc;
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
     * alterTableAdd
     *
     * @param string $tablename
     * @return array
     */
    private function alterTableAdd($tablename)
    {
        return [$this->alterTable($tablename), self::_ADD];
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
        $this->_adapter = $this->dbConfig[$this->slot]['adapter'];
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
        $this->_schema = $slotDbConfig['name'];
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
