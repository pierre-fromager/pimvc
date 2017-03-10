<?php

/**
 * lib\storage
 * 
 * @author Pierre Fromager <pf@pier-infor.fr>
 */

namespace lib;

class storage {

    /**
     * Registry array of store
     * @access private
     */
    private $store = [];

    /**
     * __construct
     * 
     */
    public function __construct() {}

    /**
     * __get
     * 
     * @param string $name
     * @return mixex
     */
    public function __get($name) {
        if (isset($this->store[$name])) {
            return $this->store[$name];
        }
        return null;
    }

    /**
     * __isset
     * 
     * @param string $name
     * @return boolean
     */
    public function __isset($name) {
        return (isset($this->store[$name]));
    }

    /**
     * __set
     * 
     * @param string $name
     * @param mixex $value
     */
    public function __set($name, $value) {
        $this->store[$name] = $value;
    }
    
    /**
     * __unset
     * 
     * @param type $name
     */
    public function __unset($name) {
        unset($this->store[$name]);
    }
    
    /**
     * __toString
     * 
     * @return string
     */
    public function __toString() {
        return json_encode($this->store, JSON_PRETTY_PRINT) ;
    }

}
