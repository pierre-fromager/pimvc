<?php
/**
 * Description of Pimvc\Db\Model\Core
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */
namespace Pimvc\Db\Model;

abstract class Core extends Charset implements Interfaces\Core
{

    protected $_db;
    protected $_logger;
    protected $sql;
    protected $_error;
    protected $_errorCode;
    protected $_errorMessage;
    /**
     * $_statement
     * @var \PDOStatement
     */
    protected $_statement;
    protected $_restMode;
    protected $_fetchMode = \PDO::FETCH_ASSOC;
    protected $_schema;
    protected $_adapter;
    protected $_primary;

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
        foreach ($paArray as $k => $v) {
            $type = (is_int($v)) ? \PDO::PARAM_INT : \PDO::PARAM_STR;

            if (isset($forcedTypes[$k])) {
                $type = $forcedTypes[$k];
                $v = ($type == \PDO::PARAM_INT) ? (int) $v : $v;
            }
            $value = is_array($v) ? serialize($v) : $v;
            $key = ':' . $k;
            try {
                $poStatement->bindValue($key, $value, $type);
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
            case \Pimvc\Db\Model\Core::MODEL_ADAPTER_4D:
                $sql = 'SELECT  * '
                    . ' FROM _USER_COLUMNS uc'
                    //. '  INNER JOIN _USER_CONSTRAINTS uco on uc.table_id = uco.table_id'
                    . '  WHERE uc.table_name = :tablename'; // AND uco.constraint_name = \'\''; //' AND uco.table_name = :tablename';
                $this->run($sql, ['tablename' => $this->getCharsetConvert(
                    $tablename,
                    'utf-8',
                    'utf-16'
                )]);
                $results = $this->_statement->fetchAll($this->_fetchMode);
                $this->_statement->closeCursor();
                $this->charsetConvertCollection($results);
                if (!$results) {
                    throw new \Exception('Cant describe ' . $tablename);
                }
                array_walk($results, function (&$v) {
                    $v['column_name'] = strtolower($v['column_name']);
                    $v['table_name'] = strtolower($v['table_name']);
                });
                //var_dump($results);die;
                reset($results);
                return $results;
                break;
        }
        $this->run($sql);
        $result = $this->_statement->fetchAll($this->_fetchMode);
        $this->_statement->closeCursor();
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
        $bindArray = [];
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

            case \Pimvc\Db\Model\Core::MODEL_ADAPTER_4D:
                $schemaPrefix = ($this->_schema) ? $this->_schema . '.' : '';
                $sql = 'SELECT * FROM _USER_IND_COLUMNS uic'
                    . ' JOIN _USER_INDEXES ui on ui.index_uuid = uic.index_uuid'
                    . ' WHERE uic.table_name = :tablename';
                $this->run($sql, ['tablename' => $this->getCharsetConvert(
                    $tablename,
                    'utf-8',
                    'utf-16'
                )]);
                $result = $this->_statement->fetchAll($this->_fetchMode);
                $this->_statement->closeCursor();
                $this->charsetConvertCollection($result);
                array_walk($result, function (&$v) {
                    $v['column_name'] = strtolower($v['column_name']);
                    $v['table_name'] = strtolower($v['table_name']);
                });
                reset($result);
                return $result;
                break;
        }
        $this->run($sql);
        $result = $this->_statement->fetchAll($this->_fetchMode);
        $this->_statement->closeCursor();
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
        return (count($parts) == 2) ? $parts[1] : $tablename;
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
        $bindParams = [];
        $forcedTypes = [];
        switch ($this->_adapter) {
            case \Pimvc\Db\Model\Core::MODEL_ADAPTER_MYSQL:
                $shemaTables = $informationSchema . '.TABLES';
                $condition = "($tableSchema = '$this->_schema') AND ($tableName = '$tablename')";
                $sql = self::MODEL_SELECT . '*' . self::MODEL_FROM . $shemaTables .
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
            case \Pimvc\Db\Model\Core::MODEL_ADAPTER_4D:
                $sql = 'SELECT table_name FROM _USER_TABLES '
                    . 'WHERE table_name = :tablename';
                $bindParams = ['tablename' => $this->getCharsetConvert(
                    $tablename,
                    'utf-8',
                    'utf-16'
                )
                ];
                break;
        }
        $this->run($sql, $bindParams, $forcedTypes);
        $results = $this->_statement->fetchAll($this->_fetchMode);
        $this->_statement->closeCursor();
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

            case \Pimvc\Db\Model\Core::MODEL_ADAPTER_4D:
                $sql = 'SELECT table_name FROM _USER_TABLES';
                $this->run($sql);
                $results = $this->_statement->fetchAll(\PDO::FETCH_COLUMN, 0);
                $this->charsetConvert($results);
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
    abstract protected function statementErrorPrepareChecking($sql);

    /**
     * statementErrorBind
     *
     * @param \PDOException $exc
     */
    abstract protected function statementErrorBind(\PDOException $exc, $queryType);
   

    /**
     * statementErrorExecute
     *
     * @param \PDOException $exc
     */
    abstract protected function statementErrorExecute(\PDOException $exc, $queryType);
}
