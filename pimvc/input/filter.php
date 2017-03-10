<?php

/**
 * Description of filter
 *
 * @author pierrefromager
 */

namespace lib\input;

class filter {

    const INPUT_FILTER_FILTER = 'filter';
    const INPUT_FILTER_OPTIONS = 'options';
    const INPUT_FILTER_PROCESS = 'process';

    private $filterArgs;
    private $data;
    private $prepared;
    private $result;

    /**
     * __construct
     * 
     * @param type $data
     * @param type $filterArgs
     */
    public function __construct($data, $filterArgs) {
        $this->filterArgs = $filterArgs;
        $this->data = $data;
        $this->prepare()->process();
        return $this;
    }

    /**
     * prepare
     * 
     */
    protected function prepare() {
        $this->prepared = [];
        foreach ($this->filterArgs as $k => $v) {
            if (is_object($v)) {
                $this->prepared[$k] = [
                    self::INPUT_FILTER_FILTER => FILTER_CALLBACK,
                    self::INPUT_FILTER_OPTIONS => [$v, self::INPUT_FILTER_PROCESS]
                ];
            } else {
                $this->prepared[$k] = $v;
            }
        }
        unset($this->filterArgs);
        return $this;
    }

    /**
     * process
     * 
     * @return array
     */
    public function process() {
        $this->result = \filter_var_array($this->data, $this->prepared);
        return $this;
    }

    /**
     * get
     * 
     * @return array
     */
    public function get() {
        return $this->result;
    }

    /**
     * __get
     * 
     * @param string $paramName
     * @return mixed
     */
    public function __get($paramName) {
        return $this->result[$paramName];
    }

    /**
     * __get
     * 
     * @param string $paramName
     * @return mixed
     */
    public function __isset($paramName) {
        return isset($this->result[$paramName]);
    }
    
    /**
     * __destruct
     * 
     */
    public function __destruct() {
        foreach ($this as $k => $v) {
            unset($this->$k);
        };
    }

}
