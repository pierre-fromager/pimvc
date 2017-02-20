<?php

/**
 * Description of orm
 *
 * @author pierrefromager
 */
namespace lib\db\model\exceptions;

class orm extends \Exception{
    
    const ORM_EXC_MISSING_SLOT = '_slot property missing in ';
    const ORM_EXC_MISSING_ADAPTER = 'adatper missing check db config ';

    
    public function __construct($message, $code = 0, Exception $previous = null) {
        $trace = $this->getTrace();
        $cls = $trace[0]['class'];
        $message = $cls . ": $message";
        parent::__construct($message, $code, $previous);
    }
}
