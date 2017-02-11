<?php

namespace lib;

require_once __DIR__ . DIRECTORY_SEPARATOR . 'loader.php';

// lib usage
use lib\http\routes;
use lib\http\router;
use lib\http\request;
use lib\http\response;
use lib\view;
use lib\controller;
use lib\config;

/**
 * routes
 * 
 */
class app implements interfaces\appInterface{
    
    public $config = null;
    public $routes = null;
    public $router = null;
    public $controller = null;
    public $path = null;
    public $request = null;
    public $response = null;
    public $view = null;

    /**
     * __construct
     * 
     * @param type $config
     * @return $this
     * @throws \Exception
     */
    public function __construct(config $config) {
        if (!$config && !is_array($config)) {
            throw new \Exception('Config missing');
        }
        $this->config = $config;
        $this->request = new request();
        $this->routes = new routes($config->getSettings('routes'));
        $this->router = new router($this->routes);
        $this->response = new response();
        $this->view = new view();
        $this->controller = new controller($this);
        return $this;
    }
    
    /**
     * setPath
     * 
     * @param string $path
     * @return $this
     */
    public function setPath($path) {
        $this->path = $path;
        return $this;
    }
    
    /**
     * getRouter
     * 
     * @return router
     */
    public function getRouter() {
        return $this->router;
    }

    /**
     * getRequest
     * 
     * @return request
     */
    public function getRequest() {
        return $this->request;
    }
    
    /**
     * getRoutes
     * 
     * @return routes
     */
    public function getRoutes() {
        return $this->routes;
    }
    
    /**
     * getResponse
     * 
     * @return lib\http\response
     */
    public function getResponse() {
        return $this->response;
    }
    
    /**
     * getView
     * 
     * @return lib\view
     */
    public function getView() {
        return $this->view;
    }
    
    /**
     * getPath
     * 
     * @return string
     */
    public function getPath(){
        return $this->path;
    }
    
    /**
     * getController
     * 
     * @return lib\controller
     */
    public function getController(){
        return $this->controller;
    }
    
    /**
     * getConfig
     * 
     * @return lib\config
     */
    public function getConfig() {
        return $this->config;
    }
    
    /**
     * run
     * 
     * @return type
     */
    public function run() {
        return $this->getController()->setDefault()->run()->dispatch();
    }

}
