<?php

/**
 * Description of  Pimvc\File\System\Indexer\Filter
 *
 * @author pierrefromager
 */

namespace Pimvc\File\System\Indexer;

class Filter
{
    const FILTER_EMPTY = '';
    const FILTER_SPACE = ' ';
    const FILTER_REG_QUOTE = "'";
    const FILTER_REG_OPEN = '(';
    const FILTER_REG_CLOSE = ')';
    const FILTER_REG_WILD = '.*';
    const FILTER_REGEX_CALLBACK = 'getGroup';
    
    private $filters;
    private $orders;
    public $regex;

    /**
     *  __construct
     *
     */
    public function __construct()
    {
        $this->filters = [];
        $this->regex = '';
    }
    
    /**
     * addFilter
     *
     * @param string $name
     * @param string $value
     */
    public function add($name, $value, $order = '')
    {
        $this->filters[$name] = $value;
        $position = (empty($order)) ? ($this->count()+1) : $order;
        $this->setOrder($name, $position);
        $this->setRegex();
    }
    
    /**
     * removeFilter
     *
     * @param string $name
     * @param string $value
     */
    public function remove($name)
    {
        if ($this->hasFilter($name)) {
            unset($this->filters[$name]);
            $this->setRegex();
        }
    }
       
    /**
     * get
     *
     * @param string $name
     * @return string|array
     */
    public function get($name = self::FILTER_EMPTY)
    {
        if (empty($name)) {
            return $this->filters;
        } else {
            return ($this->hasFilter($name))
                ? $this->filters[$name]
                : self::FILTER_EMPTY;
        }
    }
    
    /**
     * count
     *
     * @return int
     */
    public function count()
    {
        return count($this->filters);
    }
    
    /**
     * setOrder
     *
     * @param string $name
     * @param int $position
     */
    public function setOrder($name, $position)
    {
        $this->orders[$name] = $position;
    }

    /**
     * getRegex
     *
     * @return string
     */
    public function getRegex()
    {
        return $this->regex;
    }

    /**
     * hasFilter
     *
     * @param type $name
     * @return boolean
     */
    private function has($name)
    {
        return isset($this->filters[$name]);
    }
    
    /**
     * setRegex
     *
     */
    private function setRegex()
    {
        $values = array_values($this->filters);
        $callback = array($this, self::FILTER_REGEX_CALLBACK);
        $groups = array_map($callback, $values);
        $groups = implode('', $groups);
        $this->regex = self::FILTER_SPACE
            . self::FILTER_REG_QUOTE
            . $groups
            . self::FILTER_REG_QUOTE
            . self::FILTER_SPACE;
    }
    
    /**
     * getGroup
     *
     * @param string $value
     * @return string
     */
    private function getGroup($value)
    {
        return self::FILTER_REG_OPEN . $value . self::FILTER_REG_WILD
            . self::FILTER_REG_CLOSE;
    }
    
    /**
     * render
     *
     */
    public function render()
    {
        $sort = asort($this->orders);
        die;
    }
    
    /**
     * __destruct
     *
     */
    public function __destruct()
    {
        unset($this->filters);
        unset($this->orders);
        unset($this->regex);
    }
}
