<?php
/**
 * Description of Pimvc\Db\Model\Core
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */
namespace Pimvc\Db\Model;

class Core implements Interfaces\Core
{
    
    const MODEL_DEBUG = true;
    const MODEL_TRACE = false;
    const MODEL_ADAPTER_PGSQL = 'Pdopgsql';
    const MODEL_ADAPTER_SQLITE = 'Pdosqlite';
    const MODEL_ADAPTER_DEFAULT = 'Pdomysql';
    const MODEL_ADAPTER_4D = 'Pdo4d';
    const MODEL_ADAPTER_MYSQL = self::MODEL_ADAPTER_DEFAULT;

    protected $_db;
    protected $_logger;
    protected $sql;
    protected $_error;
    protected $_errorCode;
    protected $_errorMessage;
    protected $_statement;
    protected $_restMode;

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
            if ($queryType == self::MODEL_SELECT || $queryType == 'SHOW') {
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
            $this->statementErrorExecute($exc);
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
            if ($this->is4dAdapter()) {
                $type = $this->get4dTypeFromDomain($k);
                $value = $this->get4dValueFromType($type, $v);
                //$key = $this->getSbfHash($k, $v);
                $key = ':' . $k;
            } else {
                if (isset($forcedTypes[$k])) {
                    $type = $forcedTypes[$k];
                } else {
                    $type = (preg_match($motif, $k)) ? \PDO::PARAM_INT : \PDO::PARAM_STR;
                    $type = (is_numeric($v)) ? \PDO::PARAM_INT : \PDO::PARAM_STR;
                }
                $value = is_array($v) ? serialize($v) : $v;
                $key = ':' . $k;
                //echo 'K: ' . $key . ' , V: '. $value . ' , TYPE: ' . $type . '<br/>';
            }
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
        $realName = (empty($name)) ? $this->_name : $name;
        $cacheName = $this->_adapter . '_' . $realName;
        $cacheDescribe = new \Pimvc\Cache($cacheName, 400);
        $cacheDescribe->setPath(\Pimvc\App::getInstance()->getPath() . '/cache/Db/Metas/');

        if ($cacheDescribe->expired()) {
            $this->_name = (empty($name)) ? $this->_name : $name;
            switch ($this->_adapter) {
                case self::MODEL_ADAPTER_PGSQL:
                    $sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '" . $this->_name . "';";
                    break;
                case self::MODEL_ADAPTER_SQLITE:
                    $sql = "SELECT * FROM sqlite_master where name='" . $this->_name . "';";
                    break;
                default:
                    $sql = 'DESCRIBE ' . $this->_schema . '.' . $this->_name;
                    break;
            }

            try {
                $this->_statement = $this->_db->prepare($sql);
            } catch (\PDOException $exc) {
                echo '<p style="color:red"> Prepare : ' . $sql . '</p>';
                echo $exc->getMessage();
                die;
            }
            try {
                $this->_statement->execute();
            } catch (\PDOException $exc) {
                echo '<p style="color:red">Execute : ' . $sql . '</p>';
                echo $exc->getMessage();
                die;
            }
            $result = $this->_statement->fetchAll($this->_fetchMode);
            if ($this->_adapter == self::MODEL_ADAPTER_SQLITE) {
                $topStrip = "CREATE TABLE '" . $this->_name . "' (";
                $striped = str_replace($topStrip, '', $result[0]['sql']);
                $striped = str_replace(')', '', $striped);
                $striped = str_replace(", '", ',', $striped);
                $striped = str_replace("'", '', $striped);
                $arrStripped = explode(',', $striped);
                $finalArray = [];
                foreach ($arrStripped as $column) {
                    $colInfo = explode(' ', $column);
                    $finalArray[] = array(
                        self::MODEL_INDEX_FIELD => str_replace("'", '', $colInfo[0])
                        , 'Type' => strtolower($colInfo[1])
                    );
                }
                $result = $finalArray;
            }
            $cacheDescribe->set($result);
        } else {
            $result = $cacheDescribe->get($cacheName);
        }
        return $result;
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
                //echo $type;
                //echo $key . ' == ' . $value . '<br>';
                $bindedKey = ':' . $key;
                //echo $bindedKey . ' == ' . $value . '('. $type. ')<br>';
                $stmt->bindParam($bindedKey, $prepValue, $type);
            }
        } catch (\PDOException $exc) {
            echo $exc->getMessage();
            echo 'EOPDSQL1';
            die;
        }
        try {
            //var_dump($stmt);
            $this->_db->beginTransaction();
            $stmt->execute();
            $this->_db->commit();
            if ($isSelect) {
                //var_dump($stmt->fetchall());
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
    protected function statementErrorExecute(\PDOException $exc)
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
            var_dump($this->_columns);
            var_dump(get_class($this->getDomainInstance()));
            echo '<p style="color:red">Execute error : '
            . $exc->getMessage() . ' EOPRUN1'
            . '<hr>' . $this->getSql()
            . '<hr>' . $exc->getTraceAsString()
            . '</p>';
            die;
        }
    }

    /**
     * get4dTypeFromDomain
     *
     * @param string $key
     * @return boolean | int
     */
    private function get4dTypeFromDomain($key)
    {
        $type = \PDO::PARAM_INT;
        $isSytem = Tools_Db_4d_Tables::isSystem($this->_name);
        $hasDefinition = $this->_domainInstance->hasPdo($key);
        if (!$isSytem && $hasDefinition) {
            return $this->_domainInstance->getPdo($key);
        }
        return $type;
    }

    /**
     * get4dValueFromType
     *
     * @param int $type
     * @param string $value
     * @return int|string
     */
    private function get4dValueFromType($type, $value)
    {
        $typedValue = $value;
        switch ($type) {
            case \PDO::PARAM_INT:
                $typedValue = (int) $value;
                break;
            case \PDO::PARAM_STR:
                $typedValue = (string) $value;
                break;
            case \PDO::PARAM_BOOL:
                $typedValue = (int) ($value == 1);
                break;
        }
        return $typedValue;
    }

    /**
     * is4dAdapter
     *
     * @return boolean
     */
    protected function is4dAdapter()
    {
        return ($this->_adapter == self::MODEL_ADAPTER_4D);
    }
}