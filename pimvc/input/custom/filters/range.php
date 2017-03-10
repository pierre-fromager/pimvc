<?php

/**
 * Description of range
 *
 * @author pierrefromager
 */

namespace pimvc\input\custom\filters;

class range extends abstracts\filters{
    
    /**
     * __construc
     * 
     * @param array $options
     */
    public function __construct($options) {
        parent::__construct($options);
    }

    /**
     * getDefault
     * 
     * @return array
     */
    protected function getDefault() {
        return [
            self::MIN_RANGE => 1,
            self::MAX_RANGE => 10,
            self::STEP => 1,
            self::_DEFAULT => false,             // Value to return on fail
            self::STRICT => false,              // Check value for correct type
            self::CAST => self::FILTER_INTEGER  // Cast the value in a certain type
        ];
    }
      
    /**
     * isValid
     * 
     * @param type $val
     * @param type $options
     * @return type
     */
    public function isValid($val) {
        return in_array(
            $val
            , range(
                $this->options[self::MIN_RANGE], 
                $this->options[self::MAX_RANGE], 
                $this->options[self::STEP]
            ), 
            $this->options[self::STRICT]
        );
    }
    
    /**
     * process
     * 
     * @param type $val
     * @return type
     */
    public function process($val) {
        $this->options = $this->getOptions($this->getDefault());
        if ($this->isValid($val, $this->options)) {
            if ($castValue = $this->castAble($val, $this->options)) {
                return $castValue;
            }
            return $val;
        } else {
            return $this->options[self::_DEFAULT];
        }
    }

}
