<?php

/**
 * Pimvc\Helper\Format\Db
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */

namespace Pimvc\Helper\Format;

use Pimvc\Html\Element\Decorator;

abstract class Db implements Interfaces\Db {

    protected $_name = '';
    protected $domainName = '';
    private $domainInstance = '';
    protected $modelName = '';
    private $modelInstance = '';
    protected $expiration = null;
    protected $useCache = false;
    protected $data = array();
    protected $keySearch = '';
    protected $keyValue = '';
    protected $filter = array();
    protected $calledName = '';
    protected $keyAggregate = array();
    protected $aggregateSeparator = ' ';
    protected $allowedKeys = array();
    protected $cache = null;
    protected $link = '';
    protected $app;

    protected function pre() {}
    protected function post() {}
    abstract public static function getInstance();
    abstract public static function getStatic($value);

    /**
     * @see  __construct
     */
    public function __construct() {
        $this->app = \Pimvc\App::getInstance();
        $this->pre();
        $this->expiration = ($this->expiration) ? $this->expiration : self::Model_Expiraton;
        $this->domainInstance = new $this->domainName;
        $this->modelInstance = new $this->modelName($this->getModelOptions());
        $this->allowedKeys = $this->domainInstance->getVars();
        $this->process();
        unset($this->domainInstance);
        unset($this->modelInstance);
        $this->post();
    }
    
    /**
     * process
     * 
     */
    private function process() {
        if ($this->isValid()) {
            $this->modelInstance->find($this->getWhat(), $this->getWhere());
            $results = $this->modelInstance->getRowsetAsArray();
            foreach ($results as $result) {
                $k = $result[$this->keySearch];
                $v = $result[$this->keyValue];
                $aggregation = ($this->keyAggregate) 
                    ? $this->aggregateSeparator . $this->getAggregateValues(
                        $result, $this->keyAggregate
                    ) 
                    : '';
                $this->data[$k] = $v . $aggregation;
            }
            unset($results);
        } else {
            $this->errorKey();
        }
    }
    
    /**
     * errorKey
     * 
     * @throws Exception
     */
    private function errorKey() {
        $message = 'Invalid Keys in Helper Db : ' . $this->domainName
            . ' for fields ' . $this->keySearch . ' with value ' . $this->keyValue
            . ' from ' . implode(' ',$this->allowedKeys);
        throw new Exception($message);
    }

    /**
     * isValid
     * 
     * @return boolean
     */
    private function isValid() {
        return ($this->isAllowed($this->keySearch) && $this->isAllowed($this->keyValue));
    }
    
    /**
     * getWhat
     * 
     * @return array
     */
    private function getWhat() {
        $what = array($this->keySearch, $this->keyValue);
        if ($this->keyAggregate) {
            $what = array_merge($what, $this->keyAggregate);
        }
        return $what;
    }

    /**
     * getWhere
     * 
     * @return array
     */
    private function getWhere() {
        return ($this->filter) 
            ? [ $this->keySearch . self::SEARCH_IN => $this->getFilterString() ] 
            : [];
    }
    
    /**
     * getFilterString
     * 
     * @return string
     */
    private function getFilterString() {
        return self::O_BRACE . implode(self::COMA, $this->filter) . self::C_BRACE;
    }

    /**
     * getModelOptions
     * 
     * @return array
     */
    private function getModelOptions() {
        return $this->app->getConfig()->getSettings(self::DB_POOL);
    }

    /**
     * getAggregateValues
     * 
     * @param array $result
     * @param array $aggregateKeys
     * @return string 
     */
    private function getAggregateValues($result, $aggregateKeys) {
        $aggregateArrayValues = array();
        foreach ($aggregateKeys as $aggregateKey) {
            if ($this->isAllowed($aggregateKey)) {
                $aggregateArrayValues[] = $result[$aggregateKey];
            }
        }
        $aggregateValues = implode($this->aggregateSeparator, $aggregateArrayValues);
        return $aggregateValues;
    }

    /**
     * isAllowed
     * 
     * @param string $keyName
     * @return boolean 
     */
    protected function isAllowed($keyName) {
        return in_array($keyName, $this->allowedKeys);
    }

    /**
     * getData
     * 
     * @return array 
     */
    public function getData() {
        return $this->data;
    }

    /**
     * getLink
     * 
     * @param int $key
     * @return string 
     */
    private function getLink($key) {
        $baseUrl = $this->app->getRequest()->getBaseUrl();
        return new Decorator(
            'a', 
            $this->data[$key],
            [
                'class' => self::LINK_CLASS,
                'href' => $baseUrl . $this->link . DIRECTORY_SEPARATOR . self::PARAM_ID 
                . DIRECTORY_SEPARATOR . $key
            ]
        );
    }

    /**
     * get
     * 
     * @param mixed $key
     * @return string 
     */
    public function get($key) {
        if (!isset($this->data[$key])) {
            throw new \Exception(self::Model_Value_Unknown . $key);
        }
        return (empty($this->link)) ? $this->data[$key] : $this->getLink($key);
    }

    /**
     * setLink
     * 
     * @param string $link
     */
    public function setLink($link) {
        $this->link = $link;
    }

}
