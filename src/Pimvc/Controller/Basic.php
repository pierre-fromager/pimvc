<?php

/**
 * Description of basicController
 *
 * @author Pierre Fromager
 */

namespace Pimvc\Controller;

use Pimvc\App;

abstract class Basic extends View implements Interfaces\Basic{
    
    /**
     * __construct
     * 
     * @param App $app
     * @param array $params
     */
    public function __construct(\Pimvc\App $app, $params = []) {
        parent::__construct($app, $params);
    }
}
