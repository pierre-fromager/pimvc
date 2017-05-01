<?php

/**
 * Description of Controller\Request : level 0
 *
 * @author Pierre Fromager
 */

namespace Pimvc\Controller;

use Pimvc\App;

abstract class Request extends Base implements Interfaces\Request {
    
    private $params;

    /**
     * __construct
     * 
     * @param App $app
     * @param array $params
     */
    public function __construct(\Pimvc\App $app, $params = []) {
        parent::__construct($app);
        $this->params = array_merge(
            $params
            , $app->getRequest()->get()['request']
        );
        $this->init();
    }
    
    /**
     * init
     * 
     */
    protected function init() {}

           
    /**
     * getParams
     * 
     * @return array
     */
    public function getParams($key = '') {
        return ($key) 
            ? isset($this->params[$key]) ? $this->params[$key] : ''
            : $this->params;
    }
    
    /**
     * hasValue
     * 
     * @param string $param
     * @return mixed
     */
    public function hasValue($param) {
        return isset($this->params[$param]);
    }
    
    /**
     * forward
     * 
     * @param string $controller
     * @param string $action
     * @param array $params
     * @return mixed
     */
    public function forward($controller = '', $action = '', $params = []) {
        $runningController = ($controller) 
            ? new $controller($this->getApp(), $params) 
            : $this;
        return ($action && method_exists($runningController, $action)) 
            ? call_user_func_array(
                [$runningController, $action]
                , $params
            ) 
            : null;
    }
    

}
