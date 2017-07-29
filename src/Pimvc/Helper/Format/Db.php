<?php

/**
 * Pimvc\Helper\Format\Db
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */

namespace Pimvc\Helper\Format;

abstract class Db {

    const Model_Expiraton = 30000;
    const Model_Value_Unknown = 'Unknown';
    const LINK_CLASS = 'format-link';

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

    /**
     * pre
     * 
     */
    protected function pre() {}

    /**
     * post
     * 
     */
    protected function post() {}

    /**
     * @see  __construct
     */
    public function __construct() {
        $this->app = \Pimvc\App::getInstance();
        $this->pre();
        $this->expiration = ($this->expiration) 
            ? $this->expiration 
            : self::Model_Expiraton;
        $this->domainInstance = new $this->domainName;
        $this->modelInstance = new $this->modelName($this->app->getConfig()->getSettings('dbPool'));
        $this->allowedKeys = $this->domainInstance->getVars();
        $isValid = ($this->isAllowed($this->keySearch) && $this->isAllowed($this->keyValue));
        if ($isValid) {
            $what = array($this->keySearch, $this->keyValue);
            if ($this->keyAggregate) {
                $what = array_merge($what, $this->keyAggregate);
            }
            if ($this->filter) {
                $list = implode(',', $this->filter);
                $where = array($this->keySearch . '#IN' => '(' . $list . ')');
            } else {
                $where = array();
            }
            $this->modelInstance->find($what, $where);
            $results = $this->modelInstance->getRowsetAsArray();
            foreach ($results as $result) {
                $k = $result[$this->keySearch];
                $v = $result[$this->keyValue];
                $aggregation = ($this->keyAggregate) 
                    ? $this->aggregateSeparator . $this->getAggregateValues(
                        $result, 
                        $this->keyAggregate
                    ) 
                    : '';
                $this->data[$k] = $v . $aggregation;
            }
        } else {
            echo 'Invalid Keys in Helper Db : ' . $this->domainName
            . ' for fields ' . $this->keySearch . '||' . $this->keyValue
            . '<hr><pre>' . print_r($this->allowedKeys, true) . '</pre>';
            die;
        }
        unset($results);
        unset($this->domainInstance);
        unset($this->modelInstance);
        $this->post();
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
        return '<a'
            . ' class="' . self::LINK_CLASS . '"'
            . ' href="' . $baseUrl . $this->link . '/id/' . $key . '">'
            . $this->data[$key]
            . '</a>';
    }

    /**
     * get
     * 
     * @param mixed $key
     * @return string 
     */
    public function get($key) {
        return (isset($this->data[$key])) 
            ? (empty($this->link)) 
                ? $this->data[$key] 
                : $this->getLink($key) 
            : self::Model_Value_Unknown . ' (' . $key . ')';
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
