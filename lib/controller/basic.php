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
    
    /**
     * redirect
     * 
     * @param string $url
     * @return lib\http\response
     */
    public function redirect($url) {
        return $this->getApp()
            ->getResponse()
            ->setContent('')
            ->setType(\lib\http\response::TYPE_HTML)
            ->setHttpCode(302)
            ->redirect($url);
    }
    
    /**
     * forward
     * 
     * @param string $name
     * @param string $action
     */
    public function forward($name, $action) {
        $this->getApp()->getController()->setName($name)->setAction($action);
    }
}
