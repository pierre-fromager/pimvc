<?php

namespace Pimvc;

/**
 * controller
 *
 */
class Controller implements Interfaces\Controller
{
    private $app;
    private $name = null;
    private $action = null;
    private $params = [];
    private $result = null;
    private $errorsMessage = [
        '',
        'Controller not found',
        'Missing class',
        'Missing method',
        'Bad router',
        'Bad routes',
        'No routes match',
        'Forbidden'
    ];
    private $errorCode = 0;
    private $errorMessage = '';
    private $errors = [];
    private $classPrefix = '';

    /**
     * __construct
     *
     * @param app $app
     * @param type $name
     * @param type $action
     */
    public function __construct(App $app = null)
    {
        $this->app = $app;
        $this->errors = [];
        return $this;
    }

    /**
     * setClassPrefix
     *
     * @param string $prefix
     */
    public function setClassPrefix($prefix)
    {
        $this->classPrefix = $prefix;
    }

    /**
     * getApp
     *
     * @return app
     */
    public function getApp()
    {
        return $this->app;
    }

    /**
     * setName
     *
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
   
    /**
     * getName
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * setAction
     *
     * @param string $action
     * @return $this
     */
    public function setAction($action)
    {
        $this->action = $action;
        return $this;
    }

    /**
     * setForbidden
     *
     * @return $this
     */
    public function setForbidden()
    {
        $this->addError(7);
        return $this;
    }

    /**
     * getPath
     *
     * @return string
     */
    public function getPath()
    {
        return $this->app->path . self::_NAMESPACE . DIRECTORY_SEPARATOR
            . str_replace(self::BACKSLASH, '/', $this->name) . self::PHP_EXT;
    }

    /**
     * check
     *
     * @param type $className
     * @return bool
     */
    public function check($className): bool
    {
        $passed = true;
        if (!$this->isError() && $fileExist = file_exists($this->getPath())) {
            include_once($this->getPath());
            $classExists = class_exists($className, false);
            $methodExists = method_exists($className, $this->action);
            if (!$methodExists || !$classExists) {
                if (!$classExists) {
                    $this->addError(2);
                }
                if (!$methodExists) {
                    $this->addError(3);
                }
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
    public function setDefault()
    {
        list($this->name, $this->action) = [
            ucfirst(self::ERROR),
            ucfirst(self::DEFAULT_ACTION)
        ];
        return $this;
    }

    /**
     * run
     *
     * @return $this
     */
    public function run()
    {
        if ($this->getApp()->getRequest()->isHome()) {
            $this->name = ucfirst(self::DEFAULT_CONTROLER);
            $this->action = ucfirst(self::DEFAULT_ACTION);
            $this->check($this->getNamespacedClass());
            $this->execute();
            return $this;
        }
        if ($matches = $this->app->getRouter()->compile()) {
            @list($this->name, $this->action, $this->params) = $matches;
            $this->name = ($this->isModuleController())
                ? $this->getModuleControllerNs()
                : ucfirst($this->name);
            $this->action = ($this->action) ? $this->action : self::DEFAULT_ACTION;
            $this->action = ucfirst($this->action);
            $this->params = ($this->params) ? $this->params : [];
            if (isset($this->params[0])) {
                if ($this->params[0] === self::QMARK) {
                    $this->params = $this->getApp()->getRequest()->getParsedQuery($this->params);
                }
                if (isset($this->params[0]) && $this->params[0] === '/') {
                    $this->params = $this->getApp()->getRequest()->getQueryTupple($this->params);
                }
            }
        } else {
            $this->addError(6);
        }
        $this->check($this->getNamespacedClass());
        if ($this->isError()) {
            $requestParams = $this->params;
            $this->params = [
                'errors' => $this->errors,
                'controller' => $this->name,
                'action' => $this->action,
                'router' => $this->app->getRouter()->compile(),
                'request' => $this->getApp()->getRequest(),
                'request_params' => $requestParams,
            ];
            $this->setDefault();
        }
        $this->execute();
        return $this;
    }
    
    /**
     * isModuleController
     *
     * @return boolean
     */
    private function isModuleController()
    {
        $isModule = (strpos($this->name, '/') > 0);
        return $isModule;
    }
    
    /**
     * getModuleControllerNs
     *
     * @return string
     */
    private function getModuleControllerNs(): string
    {
        $parts =  array_map('ucfirst', explode('/', $this->name));
        $ctrlNs = implode(self::BACKSLASH, $parts);
        return $ctrlNs;
    }

    /**
     * dispatch
     *
     */
    public function dispatch()
    {
        $className = @get_class($this->result);
        if ($className) {
            switch (get_class($this->result)) {
                case Http\Response::class:
                    $this->result->dispatch();
                    break;
            }
        }
        if (is_string($this->result)) {
            echo $this->result;
        }
    }

    /**
     * getParams
     *
     * @return array
     */
    public function getParams($key = '')
    {
        return ($key) ? $this->params[$key] : $this->params;
    }

    /**
     * getAction
     *
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * getControlerClass
     *
     * @return string
     */
    public function getControlerClass(): string
    {
        return static::class;
    }

    /**
     * setError
     *
     * @param int $errorCode
     */
    private function addError($errorCode)
    {
        $this->errorCode = $errorCode;
        $this->errorMessage = $this->errorsMessage[$this->errorCode];
        $this->errors[] = [
            self::CODE => $this->errorCode,
            self::MESSAGE => $this->errorMessage
        ];
    }

    /**
     * isError
     *
     * @return type
     */
    private function isError()
    {
        return $this->errors;
    }

    /**
     * getNamespacedClass
     *
     * @return string
     */
    private function getNamespacedClass()
    {
        $prefixable = ($this->classPrefix) ? self::BACKSLASH . $this->classPrefix : '';
        $namespace = $prefixable . self::BACKSLASH . self::_NAMESPACE
            . self::BACKSLASH . $this->name;
        return $namespace;
    }

    /**
     * execute
     *
     */
    private function execute()
    {
        $controllerNs = $this->getNamespacedClass();
        $controllerInstance = new $controllerNs($this->app, $this->params);
        $methodExist = method_exists($controllerInstance, $this->action);
        $isCallable = is_callable(array($controllerInstance, $this->action));
        if ($methodExist && $isCallable) {
            $this->result = call_user_func_array(
                [$controllerInstance, $this->action],
                is_array($this->params) ? $this->params : []
            );
        }
    }
}
