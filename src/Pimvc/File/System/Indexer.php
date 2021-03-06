<?php

/**
 * Description of Pimvc\File\System\Indexer
 *
 * @author pierrefromager
 */

namespace Pimvc\File\System;

use Pimvc\File\System\Indexer\Common as CommonIndexer;

class Indexer extends CommonIndexer
{
    
    /**
     * __construct
     *
     * @param strig $filename
     */
    public function __construct($filename)
    {
        parent::__construct($filename);
    }
    
    /**
     * __destruct
     *
     */
    public function __destruct()
    {
        parent::__destruct();
    }
}
