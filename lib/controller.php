<?php

namespace lib;

/**
 * controller
 * 
 */
class controller {

    const error = 'error';
    const _namespace = 'controller';
    const defaultAction = 'index';
    const baskSlash = '\\';
    const phpExt = '.php';
    const code = 'code';
    const message = 'message';
    
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
     * getApp
     * 
     * @return app
     */
    public function getApp() {
        return $this->app;
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
     * getNamespacedClass
     * 
     * @return string
     */
    private function getNamespacedClass() {
        return self::baskSlash  . self::_namespace 
            . self::baskSlash . $this->name;
    }

    /**
     * run
     * 
     * @return $this
     */
    public function run() {
        if ($matches = $this->app->getRouter()->compile()) {
            @list($this->name, $this->action, $this->params) = $matches;
            $this->action = ($this->action) ? $this->action : self::defaultAction;
            $this->params = ($this->params) ? $this->params : [];
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
            include_once($this->getPath());
        }
        $this->execute();
        return $this;
    }
    
    /**
     * execute
     * 
     */
    private function execute() {
        $controllerNs = $this->getNamespacedClass();
        $controllerInstance = new $controllerNs($this->app, $this->params);
        $this->result = call_user_func_array(
            [$controllerInstance, $this->action] ,
            is_array($this->params) 
                ? $this->params 
                : []
        );
    }

    /**
     * dispatch
     * 
     */
    public function dispatch() {
        switch (get_class($this->result)) {
            case 'lib\response':
                $this->result->dispatch();
                break;
            default:
                echo '<pre>' . print_r($this->result, true) . '</pre>';
                break;
        }
    }

}