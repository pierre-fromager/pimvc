<?php
/**
 * Description of Pimvc\App
 * 
 * @author Pierre Fromager <pf@pier-infor.fr>
 */

namespace Pimvc;

use Pimvc\Http\Routes;
use Pimvc\Http\Router;
use Pimvc\Http\Request;
use Pimvc\Http\Response;
use Pimvc\View;
use Pimvc\Controller;
use Pimvc\Config;
use Pimvc\Storage;

class App implements Interfaces\App{
    
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
    public function __construct(\Pimvc\Config $config) {
        $this->setConfig($config);
        $this->request = new Request();
        $this->request->startSession();
        $this->routes = new Routes($this->getConfig()->getSettings('routes'));
        $this->router = new Router($this->routes);
        $this->response = new Response();
        $this->view = new View();
        $classPrefix = $this->getConfig()->getSettings('classes')['prefix'];
        $this->controller = new Controller($this);
        $this->controller->setClassPrefix($classPrefix);
        $this->hash = spl_object_hash($this);
        $this->storage = new Storage();
        self::$instance = $this;
        return $this;
    }
    
    /**
     * getInstance
     * 
     * @return \Pimvc\App
     */
    public static function getInstance() {
        return (self::$instance instanceof \Pimvc\App) ? self::$instance : false;
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
     * @return Pimvc\Http\Response
     */
    public function getResponse() {
        return $this->response;
    }
    
    /**
     * getView
     * 
     * @return Pimvc\View
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
     * @return Pimvc\Controller
     */
    public function getController(){
        return $this->controller;
    }
    
    /**
     * getConfig
     * 
     * @return Pimvc\Config
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
