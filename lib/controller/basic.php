<?php

/**
 * Description of basicController
 *
 * @author Pierre Fromager
 */

namespace lib\controller;

require_once __DIR__ . '/interfaces/basic.php';

use lib\app;

abstract class basicController implements interfaces\basicInterface{
    
    private $params;
    private $app;

    /**
     * __construct
     * 
     * @param app $app
     * @param array $params
     */
    public function __construct(app $app, $params) {
        $this->app = $app;
        $this->params = $params;
    }
    
    /**
     * getApp
     * 
     * @return app
     */
    public function getApp() {
        return $this->app;
    }
       
    /**
     * getParams
     * 
     * @return array
     */
    public function getParams($key = '') {
        return ($key) ? $this->params[$key] : $this->params;
    }
}
