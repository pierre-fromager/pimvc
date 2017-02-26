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

    /**
     * __construct
     * 
     * @param type $data
     * @param type $filterArgs
     */
    public function __construct($data, $filterArgs) {
        $this->filterArgs = $filterArgs;
        $this->data = $data;
        $this->prepare();
        return $this;
    }
    
    /**
     * prepare
     * 
     */
    protected function prepare() {
        $this->prepared = [];
        foreach ($this->filterArgs as $key => $value) {
            $this->prepared[$key] = [
                self::INPUT_FILTER_FILTER => FILTER_CALLBACK,
                self::INPUT_FILTER_OPTIONS => [$value, self::INPUT_FILTER_PROCESS]
            ];
        }
        unset($this->filterArgs);
    }

    /**
     * get
     * 
     * @return array
     */
    public function get() {
        return \filter_var_array($this->data, $this->prepared);
    }

}