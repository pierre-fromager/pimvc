<?php

/**
 * Description of basicController
 *
 * @author Pierre Fromager
 */

namespace Pimvc\Controller;

use Pimvc\App;

abstract class Basic implements Interfaces\Basic{
    
    private $params;
    private $app;

    /**
     * __construct
     * 
     * @param app $app
     * @param array $params
     */
    public function __construct(App $app, $params) {
        $this->app = $app;
        $this->params = $params;
        $this->init();
    }
    
    /**
     * init
     * 
     */
    protected function init() {}

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
     * @return Pimvc\Http\Response
     */
    public function redirect($url) {
        return $this->getApp()
            ->getResponse()
            ->setContent('')
            ->setType(\Pimvc\Http\Response::TYPE_HTML)
            ->setHttpCode(302)
            ->redirect($url);
    }
    
    /**
     * call
     * 
     * @param string $action
     * @param array $params
     * 
     * @return mixed
     */
    public function forward($controller = '', $action = '', $params = []) {
        $controller = ($controller) ? new $controller() : $this;
        return ($action && method_exists($controller, $action)) 
            ? call_user_func_array(
                array($controller, $action)
                , $params
            ) 
            : null;
    }

}
