<?php

/**
 * Description of pimvc\file\system\indexer
 *
 * @author pierrefromager
 */

namespace pimvc\file\system;

use pimvc\file\system\indexer\_abstract as abstractIndexer;

class indexer extends abstractIndexer{
    
    /**
     * __construct
     * 
     * @param strig $filename 
     */
    public function __construct($filename) {
        parent::__construct($filename);
    }
    
    /**
     * __destruct
     * 
     */
    public function __destruct() {
        parent::__destruct();
    }
}
