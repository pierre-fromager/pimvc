<?php

namespace lib;

/**
 * controller
 * 
 */
class controller implements interfaces\controller{

    private $app;
    private $name = null;
    private $action = null;
    private $params = [];
    private $result = null;
    private $errorsMessage = [
        '' ,
        'Controller not found' ,
        'Missing class' ,
        'Missing method' ,
        'Bad router' ,
        'Bad routes' ,
        'No routes match'
    ];
    private $errorCode = 0;
    private $errorMessage = '';
    private $errors =  [];
    private $classPrefix = '';

    /**
     * __construct
     * 
     * @param app $app
     * @param type $name
     * @param type $action
     */
    public function __construct(app $app = null) {
        $this->app = $app;
        $this->errors = [];
        return $this;
    }
    
    /**
     * setClassPrefix
     * 
     * @param string $prefix
     */
    public function setClassPrefix($prefix) {
        $this->classPrefix = $prefix;
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
     * setName
     * 
     * @param string $name
     * @return $this
     */
    public function setName($name) {
        $this->name = $name;
        return $this;
    }
    
    /**
     * setAction
     * 
     * @param string $action
     * @return $this
     */
    public function setAction($action) {
        $this->action = $action;
        return $this;
    }
    
    /**
     * getPath
     * 
     * @return string
     */
    public function getPath() {
        return $this->app->path . DIRECTORY_SEPARATOR . self::_namespace 
            . DIRECTORY_SEPARATOR . $this->name . self::phpExt;
    }
    
    /**
     * check
     * 
     * @param string $className
     */
    public function check($className) {
        $passed = true;
        if (!$this->isError() && $fileExist = file_exists($this->getPath())) {
            include_once ($this->getPath());
            $classExists = class_exists($className, false);
            $methodExists = method_exists($className, $this->action);
            if (!$methodExists || !$classExists) {
                if (!$classExists) $this->addError(2);
                if (!$methodExists) $this->addError(3);
                $passed = false;
            }
        } else {
            $this->addError(1);
            $passed = false;
        }
        return $passed;
    }
    
    /**
     * setDefault
     * 
     * @return $this
     */
    public function setDefault() {
        list($this->name, $this->action) = [self::error, self::defaultAction];
        return $this;
    }
    
    /**
     * run
     * 
     * @return $this
     */
    public function run() {
        if ($this->getApp()->getRequest()->isHome()) {
            $this->name = self::defaultController;
            $this->action = self::defaultAction;
            $this->check($this->getNamespacedClass());
            $this->execute();
            return $this;
        }
        if ($matches = $this->app->getRouter()->compile()) {
            @list($this->name, $this->action, $this->params) = $matches;
            $this->action = ($this->action) ? $this->action : self::defaultAction;
            $this->params = ($this->params) ? $this->params : [];
            if (isset($this->params[0]) && $this->params[0] == self::questionMark) {
                $this->params = $this->getApp()->getRequest()->getParsedQuery($this->params);
            }
            if (isset($this->params[0]) && $this->params[0] == '/') {
                $this->params = $this->getApp()->getRequest()->getQueryTupple($this->params);
            }
        } else {
            $this->addError(6);
        }
        $passed = $this->check($this->getNamespacedClass());
        if ($this->isError()) {
            $this->params = [
                'errors' => $this->errors ,
                'controller' => $this->name ,
                'action' => $this->action ,
                'request' => $this->app->request
            ];
            $this->setDefault();
        }
        $this->execute();
        return $this;
    }

    /**
     * dispatch
     * 
     */
    public function dispatch() {
        $className = @get_class($this->result);
        if ($className) {
            switch (get_class($this->result)) {
                case http\response::class:
                    $this->result->dispatch();
                    break;
            }
        }
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
     * setError
     * 
     * @param int $errorCode
     */
    private function addError($errorCode) {
        $this->errorCode = $errorCode;
        $this->errorMessage = $this->errorsMessage[$this->errorCode];
        $this->errors[] = [
            self::code => $this->errorCode,
            self::message => $this->errorMessage
        ];
    }
    
    /**
     * isError
     * 
     * @return type
     */
    private function isError() {
        return $this->errors;
    }
    
    /**
     * getNamespacedClass
     * 
     * @return string
     */
    private function getNamespacedClass() {
        $prefixable = ($this->classPrefix) 
            ? self::baskSlash . $this->classPrefix 
            : '';
        $namespace = $prefixable . self::baskSlash . self::_namespace 
            . self::baskSlash . $this->name;
        return $namespace;
    }
    
    /**
     * execute
     * 
     */
    private function execute() {
        $controllerNs = $this->getNamespacedClass();
        $controllerInstance = new $controllerNs($this->app, $this->params);
        $methodExist = method_exists($controllerInstance, $this->action);
        $isCallable = is_callable(array($controllerInstance, $this->action));
        if ($methodExist && $isCallable) {
            $this->result = call_user_func_array(
                [$controllerInstance, $this->action] , 
                is_array($this->params) ? $this->params : []
            );
        }
    }

}