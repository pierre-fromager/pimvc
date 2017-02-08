<?php

namespace lib;

$libPath = __DIR__;

require_once $libPath . '/http/routes.php';
require_once $libPath . '/http/router.php';
require_once $libPath . '/http/request.php';
require_once $libPath . '/http/response.php';
require_once $libPath . '/controller.php';
require_once $libPath . '/controller/basic.php';
require_once $libPath . '/view.php';

use lib\http\routes;
use lib\http\router;
use lib\http\request;
use lib\http\response;
use lib\view;
use lib\controller;

/**
 * routes
 * 
 */
class app {
    
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
     * @param array $routes
     * @return $this
     */
    public function __construct($routes = null) {
        if (!$routes) {
            throw new \Exception('Routes missing');
        }
        $this->request = new request();
        $this->routes = new routes($routes);
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
     * @return response
     */
    public function getResponse() {
        return $this->response;
    }
    
    /**
     * getView
     * 
     * @return view
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
     * run
     * 
     * @return type
     */
    public function run() {
        return $this->controller->setDefault()->run()->dispatch();
    }
}
