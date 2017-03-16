<?php

/**
 * Description of pimvc\app
 * 
 * @author Pierre Fromager <pf@pier-infor.fr>
 */

namespace pimvc;

use pimvc\http\routes;
use pimvc\http\router;
use pimvc\http\request;
use pimvc\http\response;
use pimvc\view;
use pimvc\controller;
use pimvc\config;
use pimvc\storage;



class app implements interfaces\app{
    
    public $hash;
    private static $instance = null;
    public $storage = null;
    public $config = null;
    public $routes = null;
    public $router = null;
    public $controller = null;
    public $path = null;
    public $request = null;
    public $response = null;
    public $view = null;
    public $db = null;

    /**
     * __construct
     * 
     * @param type $config
     * @return $this
     * @throws \Exception
     */
    public function __construct(\pimvc\config $config) {
        $this->setConfig($config);
        $this->request = new request();
        $this->request->startSession();
        $this->routes = new routes($this->getConfig()->getSettings('routes'));
        $this->router = new router($this->routes);
        $this->response = new response();
        $this->view = new view();
        $classPrefix = $this->getConfig()->getSettings('classes')['prefix'];
        $this->controller = new controller($this);
        $this->controller->setClassPrefix($classPrefix);
        $this->hash = spl_object_hash($this);
        $this->storage = new storage();
        self::$instance = $this;
        return $this;
    }
    
    /**
     * getInstance
     * 
     * @return \pimvc\app
     */
    public static function getInstance() {
        return (self::$instance instanceof \pimvc\app) ? self::$instance : false;
    }
    
    /**
     * setConfig
     * 
     * @param config $config
     * @throws \Exception
     */
    private function setConfig(config $config) {
        if (!$config && !is_array($config)) {
            throw new \Exception('Config error');
        }
        $this->config = $config;
    }
    
    /**
     * setPath
     * 
     * @param string $path
     * @return $this
     */
    public function setPath($path) {
        define('APP_PATH', $path);
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
     * @return pimvc\http\response
     */
    public function getResponse() {
        return $this->response;
    }
    
    /**
     * getView
     * 
     * @return pimvc\view
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
     * @return pimvc\controller
     */
    public function getController(){
        return $this->controller;
    }
    
    /**
     * getConfig
     * 
     * @return pimvc\config
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
