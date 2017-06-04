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
use Pimvc\Http\Middleware;
use Pimvc\View;
use Pimvc\Controller;
use Pimvc\Config as appConfig;
use Pimvc\Storage;
use Pimvc\Tools\Translate\Adapter\Csv as translatorAdpater;
use Pimvc\Tools\Translator as translate;

class App implements Interfaces\App{
    
    public $hash;
    private static $instance = null;
    public $logger = null;
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
    public $locale = null;
    public $translator = null;
    public $middlewareItems = [];
    public $middleware;

    /**
     * __construct
     * 
     * @param type $config
     * @return $this
     * @throws \Exception
     */
    public function __construct(appConfig $config) {
        $this->setConfig($config);
        $this->request = new Request();
        $this->request->startSession();
        $this->routes = new Routes($this->getConfig()->getSettings(self::APP_ROUTES));
        $this->router = new Router($this->routes);
        $this->response = new Response();
        $this->view = new View();
        $classPrefix = $this->getConfig()->getSettings(self::APP_CLASSES)[self::APP_PREFIX];
        $this->controller = new Controller($this);
        $this->controller->setClassPrefix($classPrefix);
        $this->hash = spl_object_hash($this);
        $this->storage = new Storage();
        $this->setLocale($this->getConfig()->getSettings(self::APP_APP)[self::APP_DEFAULT_LOCALE]);
        self::$instance = $this;
        return self::$instance;
    }
    
    /**
     * setMiddleware
     * 
     * @return $this
     */
    public function setMiddleware() {
        $object = new \stdClass(); // $object should be the controller
        $middlwaresClasses = $this->getConfig()->getSettings(self::APP_MIDDLEWARE);
        
        /*
        for ($c = 0; $c < count($middlwaresClasses); $c++) {
            $this->middlewareItems[] = new $middlwaresClasses[$c];
        }*/
        
        foreach ($middlwaresClasses as $name => $middleware) {
            $this->middlewareItems[$name] = new $middlwaresClasses[$name];

        }
        
        $this->middleware = new Middleware();
        $this->middleware->layer($this->middlewareItems)->peel(
            $object,
            function($object) {
                return $object;
            }
        );
        return $this;
    }

    /**
     * setTranslator
     * 
     * @return $this
     */
    public function setTranslator() {
        $this->translator = new translate(new translatorAdpater($this->locale));
        return $this;
    }
    
    /**
     * getTranslator
     * 
     * @return Tools\Translator
     */
    public function getTranslator() {
        return $this->translator;
    }
    
    /**
     * setLocale
     * 
     * @param string $locale
     * @return $this
     */
    public function setLocale($locale){
        $this->locale = ($locale) ? $locale : locale_get_default();
        ini_set('intl.default_locale', $locale);
        return $this;
    }
    
    /**
     * getLocale
     * 
     * @return string
     */
    public function getLocale() {
        return $this->locale;
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
     * @return Request
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
        return $this->setMiddleware()
            ->getController()
            ->setDefault()
            ->run()
            ->dispatch();
    }

}
