<?php
/**
 * Description of Pimvc\Db\Model\Core
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */
namespace Pimvc\Db\Model;

class Core implements Interfaces\Core
{

    protected $_db;
    protected $_logger;
    protected $sql;
    protected $_error;
    protected $_errorCode;
    protected $_errorMessage;
    protected $_statement;
    protected $_restMode;
    protected $_fetchMode = \PDO::FETCH_ASSOC;
    protected $_schema = '';
    protected $_adapter = null;
    protected $_primary = null;

    /**
     * run
     *
     * @param string $sql
     * @param array $bindParams
     * @return boolean
     */
    public function run($sql, $bindParams = [], $bindTypes = [])
    {
        if (self::MODEL_TRACE) {
            $this->_logger->logDebug('Sql run', $sql);
        }
        $this->sql = $sql;
        $queryType = $this->getQueryType($sql);

        $returnCode = false;
        $this->_error = '';
        try {
            $this->_statement = $this->_db->prepare($sql);
            $this->statementErrorPrepareChecking($sql);
            if ($queryType == self::MODEL_SELECT || $queryType == 'SHOW' || $queryType == 'DESCRIBE') {
                $this->_statement->setFetchMode($this->_fetchMode);
            }
            if ($bindParams) {
                $this->bindArray($this->_statement, $bindParams, $bindTypes);
            }
            if (self::MODEL_DEBUG) {
                $this->_logger->logDebug(
                    'Sql Bind ' . $queryType,
                    $this->_statement->queryString
                );
            }
        } catch (\PDOException $exc) {
            $this->statementErrorBind($exc, $queryType);
        }
        try {
            $this->_statement->execute();
        } catch (\PDOException $exc) {
            $this->statementErrorExecute($exc, $queryType);
        }
        return $returnCode;
    }

    /**
     * bindArray binds Pdo values for query prepare
     *
     * @param PDOStatement $poStatement
     * @param array $paArray
     */
    public function bindArray(\PDOStatement &$poStatement, &$paArray, $forcedTypes = [])
    {
        $motif = '/_' . $this->_primary . '$|id|code/';
        foreach ($paArray as $k => $v) {
            $type = (preg_match($motif, $k)) ? \PDO::PARAM_INT : \PDO::PARAM_STR;
            if (isset($forcedTypes[$k])) {
                $type = $forcedTypes[$k];
            } else {
                $type = (preg_match($motif, $k)) ? \PDO::PARAM_INT : \PDO::PARAM_STR;
                $type = (is_numeric($v)) ? \PDO::PARAM_INT : \PDO::PARAM_STR;
            }
                $value = is_array($v) ? serialize($v) : $v;
                $key = ':' . $k;
            try {
                $poStatement->bindValue($key, $value, $type);
                if (self::MODEL_DEBUG) {
                    $this->_logger->logDebug(
                        'Bind key (' . $key . ')',
                        'value (' . $value . ') and type (' . $type . ')'
                    );
                }
            } catch (\PDOException $exc) {
                $this->_logger->logError(
                    'Sql Bind Error [' . $key . ':' . $value . ':' . $type . ']',
                    $exc->getMessage()
                );
            }
        }
        return $this;
    }

    /**
     * describeTable
     * returns the current table description
     *
     * @return array
     */
    public function describeTable($name = '')
    {
        $this->_name = (empty($name)) ? $this->_name : $name;
        $tablename = (strpos($this->_name, '.') > 0) ? $this->removeSchemaFromName($this->_name) : $this->_name;
        switch ($this->_adapter) {
            case \Pimvc\Db\Model\Core::MODEL_ADAPTER_PGSQL:
                $sql = 'SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = \'' . $tablename . '\';';
                break;
            case self::MODEL_ADAPTER_SQLITE:
                $sql = "PRAGMA table_info([$tablename]);";
                break;
            case \Pimvc\Db\Model\Core::MODEL_ADAPTER_MYSQL:
                $schemaPrefix = ($this->_schema) ? $this->_schema . '.' : '';
                $sql = 'DESCRIBE ' . $schemaPrefix . $tablename;
                break;
        }
        
        $this->run($sql);
        $result = $this->_statement->fetchAll($this->_fetchMode);
        return $result;
    }

    /**
     * getIndexes
     *
     * @param string $tablename
     * @return array
     */
    public function getIndexes($tablename = '')
    {
        $tablename = (strpos($this->_name, '.') > 0) ? $this->removeSchemaFromName($this->_name) : $this->_name;
        switch ($this->_adapter) {
            case \Pimvc\Db\Model\Core::MODEL_ADAPTER_PGSQL:
                $sql = self::MODEL_SELECT . '*,t.relname, a.attname, a.attnum'
                    . self::MODEL_FROM . 'pg_index c'
                    . ' LEFT JOIN pg_class t ON c.indrelid  = t.oid'
                    . ' LEFT JOIN pg_attribute a ON a.attrelid = t.oid AND a.attnum = ANY(indkey)'
                    . "WHERE t.relname = '$tablename'";
                break;
            case self::MODEL_ADAPTER_SQLITE:
                $result = [];
                $sql = "PRAGMA index_list([$tablename]);";
                $this->run($sql);
                $resultIndexes = $this->_statement->fetchAll($this->_fetchMode);
                foreach ($resultIndexes as $row) {
                    $indexName = $row['name'];
                    $row['ixname'] = $indexName;
                    $row['primary'] = false;
                    $sql = "PRAGMA index_info([$indexName]);";
                    $this->run($sql);
                    $detailResult = $this->_statement->fetchAll($this->_fetchMode);
                    $result[] = array_merge($row, $detailResult[0]);
                }
                $pkResult = [];
                $sqlPk = "SELECT * FROM sqlite_master where tbl_name='$tablename' and sql like '%PRIMARY%'";
                $this->run($sqlPk);
                $pkResult = $this->_statement->fetchAll($this->_fetchMode);
                if (isset($pkResult[0])) {
                    $pattern = '/\(([^\)]*)\)/';
                    $sqlPkResult = $pkResult[0]['sql'];
                    $sqlPkResultMatches = preg_match($pattern, $sqlPkResult, $matches);
                    if ($sqlPkResultMatches) {
                        $rawfieldlist = str_replace(['(', "'"], '', $matches[0]);
                        $fieldlist = explode(',', $rawfieldlist);
                        foreach ($fieldlist as $field) {
                            if (strpos($field, 'PRIMARY')) {
                                $fieldParts = explode(' ', $field);
                                $fieldName = $fieldParts[0];
                                $pkDetail = [
                                    'seq' => 1000,
                                    'name' => $fieldName,
                                    'unique' => true,
                                    'origin' => 't',
                                    'partial' => 0,
                                    'ixname' => $fieldName,
                                    'primary' => true,
                                    'seqno' => 1000,
                                    'cid' => 1000
                                ];
                                $result[] = $pkDetail;
                            }
                        }
                    }
                }

                return $result;
                break;
            case \Pimvc\Db\Model\Core::MODEL_ADAPTER_MYSQL:
                $schemaPrefix = ($this->_schema) ? $this->_schema . '.' : '';
                $sql = 'SHOW INDEX FROM ' . $schemaPrefix . $tablename;
                break;
        }
        $this->run($sql);
        $result = $this->_statement->fetchAll($this->_fetchMode);
        return $result;
    }

    /**
     * removeSchemaFromName
     *
     * @param string $tablename
     * @return string
     */
    public function removeSchemaFromName($tablename)
    {
        $parts = explode('.', $tablename);
        return (count($parts) > 0) ? $parts[1] : $tablename;
    }

    /**
     * tableExist
     *
     * @param string $tablename
     * @return boolean
     */
    public function tableExist($tablename)
    {
        $informationSchema = 'INFORMATION_SCHEMA';
        $tableSchema = 'TABLE_SCHEMA';
        $tableName = 'TABLE_NAME';

        switch ($this->_adapter) {
            case \Pimvc\Db\Model\Core::MODEL_ADAPTER_MYSQL:
                $shemaTables = $informationSchema . '.TABLES';
                $condition = "($tableSchema = '$this->_schema') AND ($tableName = '$tablename')";
                $sql = self::MODEL_SELECT . 'count(*)' . self::MODEL_FROM . $shemaTables .
                    self::MODEL_WHERE . $condition;
                break;
            case \Pimvc\Db\Model\Core::MODEL_ADAPTER_SQLITE:
                $condition = "type='table' AND name='$tablename'";
                $sql = self::MODEL_SELECT . 'name' . self::MODEL_FROM . 'sqlite_master' .
                    self::MODEL_WHERE . $condition;
                break;

            case \Pimvc\Db\Model\Core::MODEL_ADAPTER_PGSQL:
                $sql = self::MODEL_SELECT . '*' . self::MODEL_FROM
                    . 'information_schema.tables' .
                    self::MODEL_WHERE . "table_catalog = '$this->_schema'"
                    . self::MODEL_AND . "table_schema = 'public'"
                    . self::MODEL_AND . "table_name = '$tablename';";
                break;
        }
        $this->run($sql);
        $results = $this->_statement->fetchAll($this->_fetchMode);
        return (count($results) > 0);
    }

    /**
     * showTables
     *
     * @return array
     */
    public function showTables()
    {
        switch ($this->_adapter) {
            case \Pimvc\Db\Model\Core::MODEL_ADAPTER_MYSQL:
                $sql = 'SHOW TABLES';
                $this->run($sql);
                $results = $this->_statement->fetchAll($this->_fetchMode);
                $tupple = [];
                foreach ($results as $result) {
                    $k = key($result);
                    $tupple[] = $this->_schema . '.' . $result[$k];
                }
                unset($results);
                return $tupple;
                break;

            case \Pimvc\Db\Model\Core::MODEL_ADAPTER_PGSQL:
                $what = "(schemaname || '.' || tablename) as tn";
                $sql = "SELECT $what FROM pg_catalog.pg_tables ";
                $this->run($sql);
                $results = $this->_statement->fetchAll($this->_fetchMode);
                $tupple = [];
                foreach ($results as $result) {
                    $k = key($result);
                    $tupple[] = $result[$k];
                }
                return $tupple;
                break;

            case \Pimvc\Db\Model\Core::MODEL_ADAPTER_SQLITE:
                $what = "name";
                $where = 'WHERE (type="table" OR type="view") AND name != "sqlite_sequence"';
                $sql = "SELECT $what FROM sqlite_master " . $where;
                $this->run($sql);
                $results = $this->_statement->fetchAll(\PDO::FETCH_COLUMN, 0);
                return $results;
                break;
        }
    }

    /**
     * directsql
     *
     * @param string $sql
     * @param array $params
     */
    public function directsql($sql, $params)
    {
        $isSelect = (strpos($sql, 'SELECT') !== false);
        try {
            $stmt = $this->_db->prepare($sql);
        } catch (\PDOException $exc) {
            echo $exc->getMessage();
            die;
        }
        try {
            foreach ($params as $key => &$value) {
                $type = ($key == 'numero') ? PDO::PARAM_STR : PDO::PARAM_INT;
                $prepValue = ($type == PDO::PARAM_STR) ? (string) $value : (int) $value;
                $bindedKey = ':' . $key;
                $stmt->bindParam($bindedKey, $prepValue, $type);
            }
        } catch (\PDOException $exc) {
            echo $exc->getMessage();
            echo 'EOPDSQL1';
            die;
        }
        try {
            $this->_db->beginTransaction();
            $stmt->execute();
            $this->_db->commit();
            if ($isSelect) {
            }
        } catch (\PDOException $exc) {
            $this->_db->rollBack();
            echo $exc->getMessage();
            echo 'EOPDSQL2';
            die;
        }
        return $this;
    }

    /**
     * getQueryType
     *
     * @param string $sql
     * @return string
     */
    public function getQueryType($sql)
    {
        $queryMembers = explode(' ', $sql);
        $queryType = (empty($queryMembers[0])) ? $queryMembers[1] : $queryMembers[0];
        return $queryType;
    }

    /**
     * getSql
     *
     * @return string
     */
    public function getSql()
    {
        return $this->sql;
    }

    /**
     * getSize
     *
     * @return int
     */
    public function getSize()
    {
        $sql = self::MODEL_SELECT . ' count(' . $this->_primary . ') '
            . self::MODEL_FROM . $this->_name;
        $results = $this->_db->query($sql);
        foreach ($results as $result) {
        }
        return $result[0];
    }

    /**
     * isPgsql
     *
     * @return boolean
     */
    protected function isPgsql()
    {
        return ($this->_adapter == self::MODEL_ADAPTER_PGSQL);
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
        if (self::MODEL_DEBUG || !$this->_restMode) {
            echo '<p style="color:red">Bind error : '
            . $exc->getMessage()
            . '</p>';
            die;
        }
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
        if ($isExecError) {
            echo '<p style="color:red">Execute error : '
            . $exc->getMessage() . ' EOPRUN1'
            . '<hr>' . $this->getSql()
            . '<hr>' . $exc->getTraceAsString()
            . '</p>';
            die;
        }
    }
}
