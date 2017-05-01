<?php

/**
 * Description of basicController
 *
 * @author Pierre Fromager
 */

namespace Pimvc\Controller;

use Pimvc\App;

abstract class Base implements Interfaces\Base{
    
    private $app;
    
    /**
     * __construct
     * 
     * @param App $app
     * @param array $params
     */
    public function __construct(App $app) {
        $this->app = $app;
    }
    
    /**
     * getApp
     * 
     * @return App
     */
    public function getApp() {
        return $this->app;
    }
       

}
