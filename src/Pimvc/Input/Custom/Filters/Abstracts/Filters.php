<?php

/**
 * Description of filters
 *
 * @author pierrefromager
 */
namespace Pimvc\Input\Custom\Filters\Abstracts;

abstract class Filters implements \Pimvc\Input\Custom\Filters\Interfaces{
    
    protected $options;

    /**
     * __construct
     * 
     * @param array $options
     */
    public function __construct($options) {
        $this->options = $options;
    }
    
    /**
     * castAble
     * 
     * @param mixed $val
     * @return boolean
     */
    protected function castAble($val) {
        if (isset($this->options[self::CAST]) && !settype($val, $this->options[self::CAST])) {
            return $this->options[self::_DEFAULT];
        }
        return $val;
    }
    
    /**
     * getOptions
     * 
     * @return array
     */
    protected function getOptions($defaultOptions){
        return array_merge($defaultOptions, $this->options);
    }

}
