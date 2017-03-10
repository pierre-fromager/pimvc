<?php

/**
 * Description of basicController
 *
 * @author Pierre Fromager
 */

namespace pimvc\controller;

use pimvc\app;

abstract class basic implements interfaces\basic{
    
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
        $this->init();
    }
    
    /**
     * init
     * 
     */
    protected function init() {}

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
    
    /**
     * redirect
     * 
     * @param string $url
     * @return pimvc\http\response
     */
    public function redirect($url) {
        return $this->getApp()
            ->getResponse()
            ->setContent('')
            ->setType(\pimvc\http\response::TYPE_HTML)
            ->setHttpCode(302)
            ->redirect($url);
    }
    
    /**
     * call
     * 
     * @param string $action
     * @param array $params
     * 
     * @return mixed
     */
    public function forward($controller = '', $action = '', $params = []) {
        $controller = ($controller) ? new $controller() : $this;
        return ($action && method_exists($controller, $action)) 
            ? call_user_func_array(
                array($controller, $action)
                , $params
            ) 
            : null;
    }

}