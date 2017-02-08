<?php

namespace lib\controller;

use lib\app;

/**
 * Description of basic
 *
 * @author Pierre Fromager
 */
abstract class basicController {
    
    private $params;
    private $app;

    /**
     * __construct
     * 
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
    protected function getApp() {
        return $this->app;
    }
       
    /**
     * getParams
     * 
     * @return array
     */
    protected function getParams($key = '') {
        return ($key) ? $this->params[$key] : $this->params;
    }
}
