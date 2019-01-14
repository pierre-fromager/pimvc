<?php
/**
 * Description of Pimvc\Db\Model\Orm
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */
namespace Pimvc\Db\Model;

use Pimvc\Db\Model\Exceptions\Orm as ormException;
use Pimvc\Db\Model\Interfaces\Orm as ormInterface;

abstract class Orm extends Core implements ormInterface
{

    protected $_config = null;
    protected $_dsn = null;
    protected $_db = null;
    protected $_slot = 'db1';
    protected $_defaultSchema = '';
    protected $_dependentTables = null;
    protected $_name = null;
    protected $_types = [];
    protected $_error = false;
    protected $_errorCode = 0;
    protected $_errorMessage = '';
    public $_rowset = null;
    public $_current = null;
    public $_currentIndex = null;
    public $_count = null;
    protected $_metas = null;
    protected $_columns = null;
    protected $_domain = null;
    protected $_domainSuffix = '';
    protected $_domainClass = null;
    /*
     * $_domainInstance
     * \Pimvc\Db\Model\Domain
     */
    protected $_domainInstance;
    protected $_logger = null;
    protected $_attributes = [];
    protected $_dependentModels = [];
    protected $_refMap = [];
    protected $_Or = [];
    protected $_parenthesis = [];
    public $_useCache = false;
    public $_cache = null;
    protected $_cachePath = '';
    protected $_cacheQuery = null;
    protected $_uid = null;
    protected $sql = '';
    protected $patchWhere = '';
    protected $_where = [];
    protected $_whereCriterias = [];
    protected $_restMode;
    protected $_casts;
    protected $_isSystem;
    private $_app;

    /**
     * __construct
     *
     * @param type $config
     * @return \Pimvc\Db\Model\Orm
     * @throws ormException
     */
    public function __construct($config = [])
    {
        $this->_app = \Pimvc\App::getInstance();
        $this->_config = $config;
        if (!isset($this->_slot)) {
            throw new ormException(ormException::ORM_EXC_MISSING_SLOT);
        }
        if (!isset($this->_config[$this->_slot]['adapter'])) {
            throw new ormException(ormException::ORM_EXC_MISSING_ADAPTER);
        }
        $this->_adapter = ucfirst(strtolower($this->_config[$this->_slot]['adapter']));
        
        $this->_logger = \Pimvc\Logger::getFileInstance(
            $this->_app->getPath() . '/log/',
            \Pimvc\Logger::DEBUG,
            \Pimvc\Logger::LOG_ADAPTER_FILE
        );
        $this->_useCache = (isset($config['useCache']) && $config['useCache'] == false) ? false : self::MODEL_USE_CACHE;
        $this->_restMode = (isset($config['restMode']) && $config['restMode'] == true);
        $this->_schema = $config[$this->_slot]['name'];
        $this->_db = \Pimvc\Db\Factory::getConnection($config[$this->_slot]);
        $this->_domainClass = $this->getDomainName();
        $isLateDomain = isset($config['lateDomain']);
        $this->_domainInstance = ($isLateDomain) ? null : new $this->_domainClass;
        $this->_schema = ($this->is4d()) ? '' : $this->_schema;
        $this->_metas = ($this->_isSystem === true) ? $this->getDomainFields() : $this->describeTable();
        $this->_columns = $this->getColumns();
        if ($this->_adapter == self::MODEL_ADAPTER_PGSQL) {
            $this->run('SET CLIENT_ENCODING TO \'UTF-8\'');
            $this->run('SET NAMES \'UTF-8\'');
        }
        $this->_casts = [];
        return $this;
    }

    /**
     * is4d
     *
     * @return bool
     */
    protected function is4d(): bool
    {
        return ($this->_adapter === self::MODEL_ADAPTER_4D);
    }

    /**
     * setSchema
     *
     * @param string $schema
     * @return \Pimvc\Db\Model\Orm
     */
    public function setSchema($schema): \Pimvc\Db\Model\Orm
    {
        $this->_schema = $schema;
        return $this;
    }

    /**
     * setCast
     *
     * force a cast on a field (pgsql only)
     *
     * @param string $fieldName
     * @param string $typeCast
     * @return \Pimvc\Db\Model\Orm
     */
    public function setCast($fieldName, $typeCast): \Pimvc\Db\Model\Orm
    {
        $this->_casts[$fieldName] = $typeCast;
        return $this;
    }

    /**
     * setCasts
     *
     * force casts on a fields array (pgsql only)
     *
     * @param array $fieldsCast
     * @return \Pimvc\Db\Model\Orm
     */
    public function setCasts($fieldsCast): \Pimvc\Db\Model\Orm
    {
        foreach ($fieldsCast as $fieldName => $typeCast) {
            $this->_casts[$fieldName] = $typeCast;
        }
        return $this;
    }

    /**
     * init
     *
     */
    protected function init()
    {
    }

    /**
     * getName
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->_name;
    }

    /**
     * getDefaultSchema
     *
     * @return string
     */
    public function getDefaultSchema(): string
    {
        return $this->_defaultSchema;
    }

    /**
     * getAdapter
     *
     * @return string
     */
    public function getAdapter(): string
    {
        return $this->_adapter;
    }

    /**
     * getStatement
     *
     * @return \PDOStatement
     */
    public function getStatement(): \PDOStatement
    {
        return $this->_statement;
    }

    /**
     * getCachename
     *
     * @param string $statement
     * @return string
     */
    protected function getCachename($name, $fieldList, $limit, $criterias): string
    {
        $limit = (is_array($limit)) ? $limit : array($limit);
        $hashTab = serialize(array_merge($limit, $criterias, $fieldList));
        return $name . '-' . md5($hashTab);
    }

    /**
     * getDomainName returns the given mapping class
     *
     * @return string
     */
    protected function getDomainName(): string
    {
        $getCalledClassNameSpliter = explode(self::BACKSLASH, get_called_class());
        $entity = array_pop($getCalledClassNameSpliter);
        array_push($getCalledClassNameSpliter, self::MODEL_DOMAIN, $entity);
        $domainName = implode(self::BACKSLASH, $getCalledClassNameSpliter);
        return $domainName;
    }

    /**
     * getDomainInstance
     *
     * @return \Pimvc\Db\Model\Domain
     */
    public function getDomainInstance(): \Pimvc\Db\Model\Domain
    {
        return $this->_domainInstance;
    }

    /**
     * setDomainInstance
     *
     * @param \Pimvc\Db\Model\Domain $domainInstance
     * @return \Pimvc\Db\Model\Orm
     */
    public function setDomainInstance(\Pimvc\Db\Model\Domain $domainInstance): \Pimvc\Db\Model\Orm
    {
        $this->_domainInstance = $domainInstance;
        return $this;
    }

    /**
     * getDomainFields return fields as defined in domain object
     *
     * @return array
     */
    protected function getDomainFields($size = 0): array
    {
        $objectVars = get_object_vars($this->_domainInstance);
        $fields = array_keys($objectVars);
        $size = ($size == 0) ? count($fields) : $size;
        $fields = array_slice($fields, 0, $size);
        $formatedFields = [];
        foreach ($fields as $field) {
            if ($field != 'counter') {
                $formatedFields[] = array(self::MODEL_INDEX_FIELD => $field);
            }
        }
        return $formatedFields;
    }

    /**
     * getMetasInfo returns meta infos
     *
     * @param string $info
     * @return array
     */
    protected function getMetasInfo($info = null): array
    {
        $result = [];
        
        if (!empty($info)) {
            foreach ($this->_metas as $meta) {
                if (isset($meta[$info])) {
                    $result[] = $meta[$info];
                }
            }
        } else {
            $result = $this->_metas;
        }
        return $result;
    }

    /**
     * getColumns returns columns name
     *
     * @return array
     */
    public function getColumns(): array
    {
        $key = self::MODEL_INDEX_FIELD;
        switch ($this->_adapter) {
            case self::MODEL_ADAPTER_PGSQL:
                $key = 'column_name';
                break;
            case self::MODEL_ADAPTER_SQLITE:
                $key = self::MODEL_INDEX_FIELD;
                break;
            case self::MODEL_ADAPTER_MYSQL:
                $key = self::MODEL_INDEX_FIELD;
                break;
            case self::MODEL_ADAPTER_4D:
                $key = 'column_name';
                break;
        }
        return array_map('strtolower', $this->getMetasInfo($key));
    }

    /**
     * setFetchMode
     *
     * @param int $mode
     * @return \Pimvc\Db\Model\Orm
     */
    public function setFetchMode(int $mode): \Pimvc\Db\Model\Orm
    {
        $this->_fetchMode = $mode;
        return $this;
    }

    /**
     * getFetchMode
     *
     * @param int $mode
     */
    public function getFetchMode(): int
    {
        return $this->_fetchMode;
    }

    /**
     * getPrimary
     * returns primary key name
     *
     * @return string
     */
    public function getPrimary(): string
    {
        if (!$this->_primary) {
            return $this->_columns[0];
        }
        return $this->_primary;
    }

    /**
     * setPrimary
     *
     *
     * @param string $pkName
     * @return \Pimvc\Db\Model\Orm
     */
    public function setPrimary(string $pkName): \Pimvc\Db\Model\Orm
    {
        $this->_primary = $pkName;
        return $this;
    }

    /**
     * getError
     *
     * @return string
     */
    public function getError(): string
    {
        return $this->_error;
    }

    /**
     * hasError
     *
     * @return bool
     */
    public function hasError(): bool
    {
        return ($this->_errorCode != 0);
    }

    /**
     * getErrorCode
     *
     * @return int
     */
    public function getErrorCode(): int
    {
        return $this->_errorCode;
    }

    /**
     * getErrorMessage
     *
     * @return string
     */
    public function getErrorMessage(): string
    {
        return $this->_errorMessage;
    }

    /**
     * isNew returns true if domain object id is null
     *
     * @return bool
     */
    protected function isNew(): bool
    {
        return (empty($this->_current->{$this->_primary}));
    }

    /**
     * getRow return row from row $value index from rowset
     *
     * @param int $value
     * @return mixed
     */
    protected function getRow($value = 0)
    {
        return $this->_rowset[$value];
    }

    /**
     * cleanRowset
     * empty _rowset
     * @return \Pimvc\Db\Model\Orm
     */
    public function cleanRowset(): \Pimvc\Db\Model\Orm
    {
        $this->_rowset = [];
        return $this;
    }

    /**
     * getRowset return rowset as array of domain object
     *
     * @return mixed
     */
    public function getRowset()
    {
        return $this->_rowset;
    }

    /**
     * getRowsetAsArray returns rowset as array without domain object
     *
     * @return array
     */
    public function getRowsetAsArray($preservedKey = '', $assignedKeyValue = ''): array
    {
        $result = [];
        if (!empty($this->_rowset)) {
            if (empty($preservedKey)) {
                foreach ($this->_rowset as $key => $value) {
                    $result[] = (array) $value;
                }
            } else {
                if (empty($assignedKeyValue)) {
                    foreach ($this->_rowset as $key => $value) {
                        $result[$value->$preservedKey] = (array) $value;
                    }
                } else {
                    foreach ($this->_rowset as $key => $value) {
                        $result[$value->$preservedKey] = $value->$assignedKeyValue;
                    }
                }
            }
        }
        return $result;
    }

    /**
     * setCurrent
     *
     * set current to rowset current index.
     * @return \Pimvc\Db\Model\Orm
     */
    protected function setCurrent(): \Pimvc\Db\Model\Orm
    {
        $this->_current = $this->getRow($this->_currentIndex);
        return $this;
    }

    /**
     * getCurrent returns current object
     *
     * @return mixed
     */
    public function getCurrent()
    {
        return $this->_current;
    }

    /**
     * seekable return true if seeking is available
     *
     * @param int $value
     * @return bool
     */
    protected function seekable(int $value = 0): bool
    {
        return (isset($this->_rowset[$value]));
    }

    /**
     * previous set _current to previous row rowset
     *
     * @return bool
     */
    public function previous(): bool
    {
        $isPrevious = $this->seekable($this->_currentIndex - 1);
        if ($isPrevious) {
            --$this->_currentIndex;
            $this->setCurrent();
        }
        return $isPrevious;
    }

    /**
     * previous set _current to previous row rowset
     *
     * @return bool
     */
    public function next(): bool
    {
        $isNext = $this->seekable($this->_currentIndex + 1);
        if ($isNext) {
            ++$this->_currentIndex;
            $this->setCurrent();
        }
        return $isNext;
    }
    
    /**
     * rewind
     *
     * set _current to first row rowset
     *
     * @return \Pimvc\Db\Model\Orm
     */
    public function rewind(): \Pimvc\Db\Model\Orm
    {
        $this->_currentIndex = 0;
        $this->setCurrent();
        return $this;
    }

    /**
     * seek current object from rowset
     *
     * @param int $value
     * @throws Exception
     */
    public function seek($value = 0)
    {
        if ($this->seekable($value)) {
            $this->_currentIndex = $value;
            $this->setCurrent();
        } else {
            $this->_error = true;
            //throw new Exception('Nothing to seek ,use find before seeking.');
        }
        return $this;
    }

    /**
     * save current object
     *
     * @throws Exception
     */
    public function save($domainObject, $forceAsNew = false, array $forcedTypes = [])
    {
        if (is_array($domainObject)) {
            $domainObjetDirty = $this->getDomainInstance();
            $domainObjetDirty->hydrate($domainObject);
            $domainObject = $domainObjetDirty;
        }
        $isValid = ($domainObject instanceof $this->_domainClass);
        if ($isValid) {
            $pk = $this->getPrimary();
            $isNew = ($forceAsNew === false) ? empty($domainObject->$pk) : $forceAsNew;
            if ($isNew) {
                if (property_exists($domainObject, 'counter')) {
                    unset($domainObject->counter);
                }
                $this->insert($domainObject->toArray());
            } else {
                $this->update($domainObject->toArray(), $forcedTypes);
            }
        } else {
            $error = 'Current domain object ' . $this->_domainClass . ' failed';
            throw new \Exception($error);
        }
        return $this;
    }

    /**
     * saveDiff
     *
     * update only properties changed from domain object
     *
     * @throws Exception
     */
    public function saveDiff($domainObject, $forceAsNew = false)
    {
        $returnCode = false;
        if (is_array($domainObject)) {
            $domainObjetDirty = $this->getDomainInstance();
            $domainObjetDirty->hydrate($domainObject);
            $domainObject = $domainObjetDirty;
        }
        $pk = $this->getPrimary();
        $isValid = ($domainObject instanceof $this->_domainClass);
        if ($isValid) {
            $isNew = ($forceAsNew === false) ? empty($domainObject->$pk) : false;
            if ($isNew) {
                if (property_exists($domainObject, 'counter')) {
                    unset($domainObject->counter);
                }
                $this->insert((array) $domainObject);
            } else {
                $this->cleanRowset();
                $where = array($pk => $domainObject->$pk);
                $this->find([], $where);
                $initialObject = $this->_current;
                $lastObject = $domainObject;
                $updatedDatas = $this->getDiffDomainObject(
                    $lastObject,
                    $initialObject
                );
                if ($updatedDatas) {
                    $updatedDatas[$pk] = $domainObject->$pk;
                    $this->update($updatedDatas);
                }
            }
        } else {
            $error = 'Current domain object ' . $this->_domainClass . ' failed';
            throw new \Exception($error);
        }
        return $this;
    }

    /**
     * compareDomainObject
     *
     * @param Lib_Db_Model_Domain_Abstract $o1
     * @param Lib_Db_Model_Domain_Abstract $o2
     */
    public function getDiffDomainObject(
        \Pimvc\Db\Model\Domain $o1,
        \Pimvc\Db\Model\Domain $o2
    ) {
        return \Pimvc\Tools\Arrayproto::recursive_array_diff(
            (array) $o1,
            (array) $o2
        );
    }

    /**
     * setOr
     *
     * @param array $params
     */
    public function setOr($params)
    {
        $this->_Or = $params;
        return $this;
    }

    /**
     * setParenthesis
     *
     * @param array $params
     */
    public function setParenthesis($params)
    {
        $this->_parenthesis = $params;
        return $this;
    }

    /**
     * isOperator returns true if operator present
     *
     * @param string $spliter
     * @param string $key
     * @return boolean
     */
    private function isOperator($spliter, $key)
    {
        return ((strpos($key, $spliter) !== false));
    }

    /**
     * _getOperator returns operator for a given key
     *
     * @param string $params
     */
    private function _getOperator(&$key, $value)
    {
        $wildcardPattern = strpbrk($value, self::MODEL_OPERATOR_TRIGGER);
        $hasWirldcard = !empty($wildcardPattern);
        if ($this->isOperator(self::MODEL_OPERATOR_SPLITER, $key)) {
            $expr = explode(self::MODEL_OPERATOR_SPLITER, $key);
            $operator = ' ' . $expr[1] . ' ';
            $key = $expr[0];
            $isEqual = ($operator == ' = ');
            $operator = ($isEqual && $hasWirldcard) ? self::MODEL_LIKE : $operator;
        } else {
            $operator = ($hasWirldcard) ? self::MODEL_LIKE : self::MODEL_EQUAL;
        }
        return $operator;
    }

    /**
     * cleanCriterias cleans criterias for binding striping operators value
     *
     * @param array $criterias
     */
    private function cleanCriterias(&$criterias)
    {
        $cleanExclude = array('in', '!in', 'bool', 'between');
        foreach ($criterias as $key => $value) {
            if ($this->isOperator(self::MODEL_OPERATOR_SPLITER, $key)) {
                $expr = explode(self::MODEL_OPERATOR_SPLITER, $key);
                $column = $expr[0];
                $operator = trim($expr[1]);
                unset($criterias[$key]);
                if (!in_array($operator, $cleanExclude)) {
                    $criterias[$column] = $value;
                }
            }
        }
        return $this;
    }

    /**
     * getSbfHash
     *
     * @param string $columnName
     * @param string||int $value
     * @return string
     */
    private function getSbfHash($columnName, $value)
    {
        $sbf = self::MODEL_TRANS . md5($columnName);
        if (self::MODEL_DEBUG) {
            $this->_logger->logInfo('Model Sbf ' . $columnName, $sbf . '=' . $value);
        }
        return $sbf;
    }

    /**
     * Creates a where clause array with bind parameters for the given
     * criteria.
     *
     * @param array $criteria key/value criteria
     *
     * @return array where clauses / bind parameters
     */
    private function _getWhere($criterias)
    {
        $where = '';
        $excludeBind = array('in', '!in', 'bool', 'between');
        if (!empty($criterias)) {
            $result = [];
            $is4d = ($this->_adapter == self::MODEL_ADAPTER_4D);
            foreach ($criterias as $column => $value) {
                $castType = (isset($this->_casts[$column])) ? '::' . $this->_casts[$column] : '';
                if ($castType) {
                    //var_dump($castType);die;
                }
                $operator = $this->_getOperator($column, $value);
                $opclean = strtolower(trim($operator));
                if (in_array($opclean, $excludeBind)) {
                    if ($opclean[0] == '!') {
                        $operator = ' not in';
                    }
                    $operator = str_replace('bool', '=', $operator);
                    $key = $column . $operator . $value;
                    $result[$key] = $value;
                } else {
                    $key = ($is4d)
                        //? self::squareBracketField($column) . $operator . $this->getSbfHash($column, $value)
                        ? $column . ' ' . $operator . ':' . $column : $column . $castType . ' ' . $operator . ':' . $column;
                    $result[$key] = "'" . $value . "'";
                }
            }

            $where = self::MODEL_WHERE . implode(self::MODEL_AND, array_keys($result));
            if ($this->_Or) {
                foreach ($this->_Or as $nameOr) {
                    $where = str_replace('AND ' . $nameOr, 'OR ' . $nameOr, $where);
                }
            }
            if ($this->_parenthesis) {
                foreach ($this->_parenthesis as $open => $close) {
                    $openName = str_replace(self::MODEL_OPERATOR_SPLITER, ' ', $open);
                    $closeName = str_replace(self::MODEL_OPERATOR_SPLITER, ' ', $close);
                    $where = str_replace(
                        ' ' . $openName,
                        ' ' . self::MODEL_PARENTH_O . $openName,
                        $where
                    );
                    $secondTerm = ':' . $closeName;
                    $where = str_replace(
                        $secondTerm,
                        ':' . $closeName . self::MODEL_PARENTH_C,
                        $where
                    );
                }
            }
        }
        return $where . $this->patchWhere;
    }

    /**
     * setPatchWere
     *
     * @param string $patch
     */
    public function setPatchWere($patch)
    {
        $this->patchWhere = $patch;
        return $this;
    }

    /**
     * _getOrder
     *
     * @param string $orders
     * @return string
     */
    public function _getOrder($orders)
    {
        $order = '';
        if (!empty($orders)) {
            $order = str_replace('=', ' ', self::MODEL_ORDER . http_build_query($orders));
            $order = str_replace('&', ',', $order);
        }
        return $order;
    }

    /**
     * _getLimit returns sql limit and offset optionaly
     *
     * @param mixed $limits
     *
     * @return string
     */
    public function _getLimit($limits)
    {
        $limit = '';
        if (!empty($limits)) {
            if (is_array($limits)) {
                $limitCounter = count($limits);
                $limit = self::MODEL_LIMIT . $limits[0];
                $limit .= ($limitCounter > 1) ? self::MODEL_OFFSET . $limits[1] : '';
            } else {
                $limit = self::MODEL_LIMIT . $limits;
            }
        }
        return $limit;
    }

    /**
     * sqaureBracketField returns sqaure Bracket fieldname
     *
     * @param type string
     */
    private static function squareBracketField($name)
    {
        return (strpos($name, 'é') !== false) ? '[' . $name . ']' : $name;
    }

    /**
     * getFields returns sql format fields
     *
     * @param array $param
     * @return string
     */
    private function getFields($param)
    {
        $callback = array(__CLASS__, 'squareBracketField');
        $params = (is_array($param)) ? array_map($callback, $param) : $param;
        $fields = (empty($params)) ? '*' : implode(',', $params);
        return $fields;
    }

    /**
     * getParts
     *
     * @param \Lib_Db_Model_Abstract $ri
     * @param array $where
     * @return \Lib_Db_Model_Domain_Abstract
     */
    public function getParts($ri, $where)
    {
        $partValues = [];
        $dataSlice = [];
        $found = (($cardinality = $ri->counter($where)) > 0);
        $mi = $ri->getDomainInstance();
        $result = null;
        $isMyself = $ri instanceof $this;
        if ($found && $cardinality == 1 || $isMyself) {
            $parts = $mi->countParts();
            $dataSlice = [];
            for ($c = 0; $c <= $parts; $c++) {
                $this->_useCache = false;
                $this->cleanRowset();
                $what = $mi->getPart($c);
                $ri->find($what, $where);
                $rowset = $ri->getRowsetAsArray();
                $oldDataSlice = $dataSlice;
                $dataSlice = array_merge($oldDataSlice, $rowset);
                unset($oldDataSlice);
                unset($rowset);
                $this->_useCache = true;
            }
            foreach ($dataSlice as $part) {
                foreach ($part as $key => $value) {
                    $partValues[$key] = $value;
                }
            }
            $mi->hydrate($partValues);
            return $mi;
        } elseif ($cardinality > 1) {
            //@TODO
        }
        return $result;
    }

    /**
     * getDependantObjects
     *
     * @param string $key
     * @param string $value
     * @param int $deepness
     * @return \stdClass
     */
    public function getDependantObjects($key, $value, $deepness = 0)
    {
        $result = new \stdClass();
        $what = [];
        $where = array($key => $value);
        $this->_useCache = false;
        $this->cleanRowset();
        $localAlias = $this->_alias;
            $this->find($what, $where);
        $rowset = $this->getRowset();
            $found = (isset($rowset[0]));
            $result->$localAlias = ($found) ? $rowset[0] : [];
        if ($result->$localAlias) {
            $linker = \Pimvc\Tools\Arrayproto::ota($result->$localAlias);
            foreach ($this->_refMap as $ft => $keys) {
                $pk = $keys[self::_LOCAL];
                $fk = $keys[self::_FOREIGN];
                if (!isset($linker[$pk])) {
                    $message = 'ORM Broken relation : ' . $this->_name . '::'
                        . $pk . '-> ' . $this->_refMap[self::_TABLE] . '::' . $fk;
                    throw new \Exception($message);
                }

                if (isset($linker[$pk])) {
                    $where = array($fk => $linker[$pk]);
                    $ri = new $ft(
                        \Pimvc\App::getInstance()->getConfig()->getSettings('dbPool')
                    );
                    $mi = $ri->getDomainInstance();
                    $what = [];

                    $alias = isset($keys[self::_ALIAS]) ? $keys[self::_ALIAS] : get_class($mi);
                    $hasCardinality = (isset($keys[self::_CARDINALITY]));

                    if ($hasCardinality) {
                        $ri->find($what, $where);
                        $rowset = $ri->getRowset();
                        $result->$alias = ($hasCardinality) ? $rowset : $rowset[0];
                    } else {
                        $rowset = $this->getParts($ri, $where);
                        $result->$alias = $rowset;
                    }
                    unset($mi);
                    unset($ri);
                }
            }
        }
        return $result;
    }

    /**
     * getAlias
     *
     * @return string
     */
    public function getAlias()
    {
        return $this->_alias;
    }

    /**
     * _join
     *
     * @param string $type
     * @param string $ft // FOREIGN TABLE
     * @param string $fc // FOREIGN COLUMN
     * @param string $lt // LOCAL TABLE
     * @param string $lc // LOCAL COLUMN
     * @return type
     */
    private function _join($type, $ft, $fc, $lt, $lc)
    {
        $joinPrefix = '';
        switch ($type) {
            case 'left':
                $joinPrefix = self::MODEL_JOIN_LEFT;
                break;
            case 'right':
                $joinPrefix = self::MODEL_JOIN_RIGHT;
                break;
            case 'inner':
                $joinPrefix = self::MODEL_JOIN_INNER;
                break;
            case 'outer':
                $joinPrefix = self::MODEL_JOIN_OUTER;
                break;
            case 'natural':
                $joinPrefix = self::MODEL_JOIN_NATURAL;
                break;
            case 'cross':
                $joinPrefix = self::MODEL_JOIN_CROSS;
                break;
            case 'union':
                $joinPrefix = self::MODEL_JOIN_UNION;
                break;
            case 'full':
                $joinPrefix = self::MODEL_JOIN_FULL;
                break;
        }
        $sqlJoin = $joinPrefix . self::MODEL_JOIN
            . $ft . self::MODEL_JOIN_ON . $lt . self::MODEL_DOT . $lc
            . self::MODEL_EQUAL
            . $ft . self::MODEL_DOT . $fc;
        return $sqlJoin;
    }

    /**
     * join
     *
     * @param string $ft // FOREIGN TABLE
     * @param string $fc // FOREIGN COLUMN
     * @param string $lt // LOCAL TABLE
     * @param string $lc // LOCAL COLUMN
     *
     * @return string
     */
    public function join($ft, $fc, $lt, $lc)
    {
        return $this->_join('normal', $ft, $fc, $lt, $lc);
    }

    /**
     * innerJoin
     *
     * @param string $ft // FOREIGN TABLE
     * @param string $fc // FOREIGN COLUMN
     * @param string $lt // LOCAL TABLE
     * @param string $lc // LOCAL COLUMN
     *
     * @return string
     */
    public function innerJoin($ft, $fc, $lt, $lc)
    {
        return $this->_join('inner', $ft, $fc, $lt, $lc);
    }

    /**
     * outerJoin
     *
     * @param string $ft // FOREIGN TABLE
     * @param string $fc // FOREIGN COLUMN
     * @param string $lt // LOCAL TABLE
     * @param string $lc // LOCAL COLUMN
     *
     * @return string
     */
    public function outerJoin($ft, $fc, $lt, $lc)
    {
        return $this->_join('outer', $ft, $fc, $lt, $lc);
    }

    /**
     * naturalJoin
     *
     * @param string $ft // FOREIGN TABLE
     * @param string $fc // FOREIGN COLUMN
     * @param string $lt // LOCAL TABLE
     * @param string $lc // LOCAL COLUMN
     *
     * @return string
     */
    public function naturalJoin($ft, $fc, $lt, $lc)
    {
        return $this->_join('natural', $ft, $fc, $lt, $lc);
    }

    /**
     * leftJoin
     *
     * @param string $ft // FOREIGN TABLE
     * @param string $fc // FOREIGN COLUMN
     * @param string $lt // LOCAL TABLE
     * @param string $lc // LOCAL COLUMN
     *
     * @return string
     */
    public function leftJoin($ft, $fc, $lt, $lc)
    {
        return $this->_join('left', $ft, $fc, $lt, $lc);
    }

    /**
     * rightJoin
     *
     * @param string $ft // FOREIGN TABLE
     * @param string $fc // FOREIGN COLUMN
     * @param string $lt // LOCAL TABLE
     * @param string $lc // LOCAL COLUMN
     *
     * @return string
     */
    public function rightJoin($ft, $fc, $lt, $lc)
    {
        return $this->_join('right', $ft, $fc, $lt, $lc);
    }

    /**
     * find init rowset from the select formed query
     *
     * @param array $fieldList
     * @param array $criterias
     * @param array $order
     * @param array $limit
     * @param string $group
     */
    public function find(
        $fieldList = [],
        $criterias = [],
        $orders = [],
        $limit = [],
        $group = '',
        $having = ''
    ) {
        $groupAggregateFunc = (empty($group)) ? '' : ', count(' . $group . ') as counter ';
        $groupBy = (empty($group)) ? '' : self::MODEL_GROUP_BY . $group;

        $havingAgg = ($having && $groupBy) ? ' HAVING ' . $having : '';
        $groupBy .= $havingAgg;

        $tableName = ($this->_schema) ? $this->_schema . '.' . $this->_name : $this->_name;
        $sql = self::MODEL_SELECT
            . $this->getFields($fieldList) . $groupAggregateFunc
            . self::MODEL_FROM . $tableName
            . $this->_getWhere($criterias)
            . $groupBy
            . $this->_getOrder($orders)
            . $this->_getLimit($limit);

        $mustQuery = true;
        $queryHash = $this->getCachename($this->_name, $fieldList, $limit, $criterias);
        $isCount = (strpos($sql, 'count(') !== false);
        if ($this->_cache && $this->_useCache) {
            $this->_cache->setName($queryHash);
            $mustQuery = $this->_cache->expired() || $isCount;
        }

        if ($mustQuery) {
            $forcedType = [];
            $this->cleanCriterias($criterias);
            $isCrud = (static::class == 'App1\Model\Crud');
            if ($this->is4d() && $isCrud) {
                $metasFieldsName = array_keys($criterias);
                foreach ($metasFieldsName as $name) {
                    $pdoType = $this->getDomainInstance()->getMetas($name)->getPdoType();
                    if ($pdoType == \PDO::PARAM_INT) {
                        $criterias[$name] = (int) $criterias[$name];
                    } elseif ($pdoType == \PDO::PARAM_STR) {
                        $criterias[$name] = //$this->getUtf8To16Le($criterias[$name]);
                            $this->getCharsetConvert(
                                $criterias[$name],
                                'utf-8',
                                'utf-16'
                            );
                    }
                    $forcedType[$name] = $pdoType;
                }
            }
            $this->run($sql, $criterias, $forcedType);
            //var_dump($forcedType);
            $this->hydrate();
            $this->cleanParenthesis();
            $this->cleanOr();
        }

        if ($this->_cache) {
            if ($mustQuery) {
                $this->_cache->set($this->_rowset);
            } else {
                $this->_rowset = $this->_cache->get();
                $this->seek();
            }
        }
        return $this;
    }

    /**
     * cleanParenthesis
     *
     */
    protected function cleanParenthesis()
    {
        $this->_parenthesis = [];
        return $this;
    }

    /**
     * cleanOr
     *
     */
    protected function cleanOr()
    {
        $this->_Or = [];
        return $this;
    }

    /**
     * clearRowset
     */
    private function clearRowset()
    {
        $this->_rowset = null;
        return $this;
    }

    /**
     * counter
     *
     * @param array $where
     * @return int
     */
    public function counter($where)
    {
        $mustQuery = true;
        if ($this->_cache) {
            $cacheName = 'count-' . $this->_name . md5(serialize($where));
            $this->_cache->setName($cacheName);
            $mustQuery = $this->_cache->expired();
        }

        if ($mustQuery) {
            $schemaPrefix = '';
            $sql = self::MODEL_SELECT_COUNT . '(' . $this->getPrimary() . ') '
                . self::MODEL_FROM . $schemaPrefix . $this->_name
                . $this->_getWhere($where);
            $this->cleanCriterias($where);
            $this->clearRowset();

            $forcedType = [];
            $isCrud = (static::class == 'App1\Model\Crud');
            if ($this->is4d() && $isCrud) {
                $metasFieldsName = array_keys($where);
                foreach ($metasFieldsName as $name) {
                    $pdoType = $this->getDomainInstance()->getMetas($name)->getPdoType();
                    if ($pdoType == \PDO::PARAM_INT) {
                        $where[$name] = (int) $where[$name];
                    } elseif ($pdoType == \PDO::PARAM_STR) {
                        $where[$name] = $this->getCharsetConvert(
                            $where[$name],
                            'utf-8',
                            'utf-16'
                        );
                    }
                    $forcedType[$name] = $pdoType;
                }
            }

            $this->run($sql, $where, $forcedType);
            $results = $this->_statement->fetchAll();
            $this->_statement->closeCursor();
            $this->seek();
            if ($results) {
                $counters = array_values($results[0]);
                $counter = $counters[0];
            } else {
                $counter = 0;
            }
            if ($this->_cache) {
                $this->_cache->set($counter);
            }
        } else {
            $counter = $this->_cache->get();
        }
        return $counter;
    }

    /**
     * add the mapperInstance to current
     *
     * @param mixed $mapperInstance
     */
    public function add($mapperInstance)
    {
        $this->_current = $mapperInstance;
        return $this;
    }

    /**
     * update a record into the given modelName from an associative array
     *
     * @param array $params
     * @param array $forcedTypes
     */
    public function update(array $params = [], array $forcedTypes = [])
    {
        $sql = self::MODEL_UPDATE . $this->_name . self::MODEL_SET;
        $quote = ($this->is4d()) ? '' : '`';
        $ql = ($this->is4d()) ? '[' : '';
        $qr = ($this->is4d()) ? ']' : '';
        $hasWhere = $this->hasWhere();
        $sqlFields = $params;
        if ($hasWhere) {
            $this->bindWhere();
            $where = $this->getWhere();
        } else {
            $key = $this->_primary;
            $value = $params[$key];
            $id = ':' . $this->_primary;
            $where = self::MODEL_WHERE . $quote . $ql . $key . $qr . $quote . " = " . $id;
            unset($sqlFields[$key]);
        }
        foreach ($sqlFields as $key => $value) {
            $keyBind = ':' . $key;
            $sql .= $quote . $ql . $key . $qr . $quote . ' = ' . $keyBind . ', ';
        }
        $sql = substr($sql, 0, -2) . $where . ';';
        $returnCode = $this->run($sql, $params, $forcedTypes);
        return $returnCode;
    }

    /**
     * getSbfParams
     *
     * @param array $params
     * @return array
     */
    private function getSbfParams($params)
    {
        $spbParams = [];
        foreach ($params as $key => $value) {
            $sbfTrans = $this->getSbfHash($key, $value);
            $spbParams[$sbfTrans] = $value;
        }
        return $spbParams;
    }

    /**
     * delete
     *
     * @param array $forcedTypes
     * @return \Pimvc\Db\Model\Orm
     */
    public function delete(array $forcedTypes = [])
    {
        $sql = self::MODEL_DELETE . $this->_name;
        $params = [];
        if ($this->hasWhere()) {
            $this->bindWhere();
            $where = $this->getWhere();
            $sql .= $where;
        } else {
            $id = isset($this->_current->{$this->_primary}) ? $this->_current->{$this->_primary} : '';
            if (!empty($id)) {
                $sql .= self::MODEL_WHERE . $this->_primary
                    . '= :' . $this->_primary;
                $params = [$this->_primary => $id];
            }
        }
        $this->run($sql, $params, $forcedTypes);
        return $this;
    }

    /**
     * multidelete delete multiples rows in current rowset
     *
     * @param string $modelName
     * @param array $params
     */
    public function multidelete()
    {
        $lot = [];
        foreach ($this->getRowsetAsArray() as $rowset) {
            $lot[] = $rowset[$this->_primary];
        }
        if (!empty($lot)) {
            $quoteLot = "'" . implode("','", $lot) . "'";
            $sql = self::MODEL_DELETE . $this->_name . self::MODEL_WHERE
                . $this->_primary . ' IN (' . $quoteLot . ')';
            $this->run($sql);
        }
        return $this;
    }

    /**
     * insert a record into the given modelName from an associative array
     *
     * @param array $params
     * @param boolean $forgetPrimary
     * @return boolean
     */
    public function insert($params = [], $forgetPrimary = true, $bindTypes = [])
    {
        if ($forgetPrimary) {
            unset($params[$this->_primary]);
        }
        $keys = array_keys($params);
        $tableName = $this->_name;
        if ($this->isPgsql()) {
            $tableName = $this->_schema . self::MODEL_DOT . $this->_name;
            $sqlKeys = '(' . implode(',', $keys) . ')';
        } else {
            $sqlKeys = '(`' . implode('`,`', $keys) . '`)';
        }
        $sqlValues = '(:' . implode(',:', $keys) . ')';
        $sql = self::MODEL_INSERT . $tableName . ' '
            . $sqlKeys . ' values ' . $sqlValues;
        $returnCode = $this->run($sql, $params, $bindTypes);
        return $returnCode;
    }

    /**
     * getSum
     *
     * @param string $column
     * @param array $criterias
     * @param int $precision
     * @return float
     */
    public function getSum($column, $criterias = [], $precision = 2)
    {
        return $this->getMathFn('sum', $column, $criterias, $precision);
    }

    /**
     * getAvg
     *
     * @param string $column
     * @param array $criterias
     * @param int $precision
     * @return float
     */
    public function getAvg($column, $criterias = [], $precision = 2)
    {
        return $this->getMathFn('avg', $column, $criterias, $precision);
    }

    /**
     * getMathFn
     *
     * @param string $column
     * @param array $criterias
     * @param int $precision
     * @return float
     */
    public function getMathFn($fn, $column, $criterias = [], $precision = 2, $precisionFn = 'round')
    {
        $fnAliasName = $column . '_' . $fn;
        $expr = $fn . self::MODEL_PARENTH_O . $column . self::MODEL_PARENTH_C;
        $sql = self::MODEL_SELECT
            . $this->_getPrecision($expr, $precision, $precisionFn)
            . self::MODEL_ALIAS . $fnAliasName
            . self::MODEL_FROM . $this->_name
            . $this->_getWhere($criterias);
        $this->clearRowset();
        $this->run($sql, $criterias);
        $results = $this->_statement->fetchAll();
        $this->_statement->closeCursor();
        return ($results) ? $results[0][$fnAliasName] : 0;
    }

    /**
     * _getPrecision
     *
     * @param string $expr
     * @param int $precision
     * @return string
     */
    private function _getPrecision($expr, $precision, $precisionFn)
    {
        $precision = $precisionFn . '(' . $expr . ',' . $precision . ')';
        return $precision;
    }

    /**
     * hydrate
     *
     */
    protected function hydrate()
    {
        $this->_rowset = new \SplFixedArray();
        $statementResult = $this->_statement->fetchAll($this->_fetchMode);
        $rowCount = count($statementResult);
        $this->_rowset->setSize($rowCount);
        $hydrateConvert = $this->is4d();
        for ($cpt = 0; $cpt < $rowCount; $cpt++) {
            $objMapper = clone $this->_domainInstance;
            $objMapper->hydrate($statementResult[$cpt], $hydrateConvert);
            $this->_rowset[$cpt] = $objMapper->get();
            unset($objMapper);
        }
        unset($statementResult);
        $this->_statement->closeCursor();
        $this->seek();
        return $this;
    }

    /**
     * getLastInsertId returns last inserted id
     *
     * @return string
     */
    public function getLastInsertId()
    {
        return $this->_db->lastInsertId();
    }

    /**
     * setWhere
     *
     * @param array $criterias
     */
    public function setWhere($criterias)
    {
        $this->_whereCriterias = $criterias;
        $this->_where = $this->_getWhere($criterias);
        return $this;
    }

    /**
     * getWhere
     *
     * @return string
     */
    public function getWhere($asCriterias = false)
    {
        return ($asCriterias) ? $this->_whereCriterias : $this->_where;
    }

    /**
     * hasWhere
     *
     * @return boolean
     */
    public function hasWhere()
    {
        return (boolean) count($this->_whereCriterias);
    }

    /**
     * bindWhere
     *
     */
    public function bindWhere()
    {
        $where = $this->getWhere();
        foreach ($this->_whereCriterias as $key => $value) {
            $posOp = strpos($key, self::MODEL_OPERATOR_SPLITER);
            $hasOperator = ($posOp !== false);
            $prepKey = ($hasOperator) ? substr($key, 0, $posOp) : $key;
            $where = str_replace(':' . $prepKey, "'" . $value . "'", $where);
        }
        $this->_where = $where;
        return $this;
    }

    /**
     * truncate
     *
     * @return boolean
     */
    public function truncate()
    {
        return $this->run(self::MODEL_TRUNCATE . $this->_name);
    }

    /**
     * getRefMap
     *
     * @return array
     */
    public function getRefMap()
    {
        return $this->_refMap;
    }

    /**
     * getDb
     *
     * @return \PDO
     */
    public function getDb()
    {
        return $this->_db;
    }

    /**
     * setStatement
     *
     * @param \PDOStatement $statement
     */
    public function setStatement($statement)
    {
        $this->_statement = $statement;
        return $this;
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
            //. '<hr>' . $exc->getTraceAsString()
            . '</p>';
            die;
        }
    }
}
