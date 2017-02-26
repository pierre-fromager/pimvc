<?php

/**
 * Description of filters
 *
 * @author pierrefromager
 */
namespace lib\input\custom\filters\abstracts;

abstract class filters implements \lib\input\custom\filters\interfaces{
    
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
        $isCasted = isset($this->options[self::CAST]);
        if ($isCasted) {
            settype($val, $this->options[self::CAST]);
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
