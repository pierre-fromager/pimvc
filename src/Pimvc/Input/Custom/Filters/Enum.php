<?php

/**
 * Description of range
 *
 * @author pierrefromager
 */

namespace Pimvc\Input\Custom\Filters;

class Enum extends Abstracts\Filters
{
    protected $options;
    protected $default;


    public function __construc($options)
    {
        $this->options = $options;
        $this->default = $this->getDefault($param);
    }
    
    public function getDefault($param)
    {
        return [
            self::VALUES => [],
            self::STRICT => false, // Value to return on fail
            self::_DEFAULT => null, // Check value for correct type
            self::CAST => false  // Cast the value in a certain type
        ];
    }
        
    /**
     * enumIsValid
     *
     * @param type $val
     * @param type $options
     * @return type
     */
    private function isValid($val, $options)
    {
        return (in_array($val, $options[self::VALUES], $options[self::STRICT]));
    }
    
    /**
     * enum
     *
     * @param type $val
     * @return type
     */
    public function process($val)
    {
        $options = $this->getOptions($this->getDefaultEnumOptions());
        if ($this->isValid($val, $options)) {
            if ($this->castAble($val, $options)) {
                return $options[self::_DEFAULT];
            }
            return $val;
        } else {
            return $options[self::_DEFAULT];
        }
    }

    public function getOptions(array $options)
    {
    }
}
