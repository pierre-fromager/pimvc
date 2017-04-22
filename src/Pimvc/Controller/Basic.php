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
     * @param App $app
     * @param array $params
     */
    public function __construct(App $app, $params = []) {
        $this->app = $app;
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
     * getApp
     * 
     * @return App
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
        return ($key) 
            ? isset($this->params[$key]) ? $this->params[$key] : ''
            : $this->params;
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
     * getView
     * 
     * @param array $params
     * @param string $viewPath
     * @return View
     */
    public function getView($params, $viewPath) {
        $filename = $this->getApp()->getPath() . $viewPath;
        return $this->getApp()
            ->getView()
            ->setParams($params)
            ->setFilename($filename)
            ->render();
    }
    
    /**
     * getHtmlResponse
     * 
     * @param type $view
     * @param type $cookieName
     * @param type $cookieValue
     * @return \Pimvc\Http\Response
     */
    public function getHtmlResponse($view, $cookieName = '', $cookieValue = '') {
        $response = $this->getApp()->getResponse()->setContent($view)
            ->setType(\Pimvc\Http\Response::TYPE_HTML)
            ->setHttpCode(200);
        if ($cookieName && $cookieValue) {
            $response->withCookie($cookieName, $cookieValue);
        }
        return $response;
    }
    
    /**
     * getJsonReponse
     * 
     * @param mixed $content
     * @return \Pimvc\Http\Response
     */
    public function getJsonReponse($content) {
        return $this->getApp()->getResponse()
            ->setContent($content)
            ->setType(\Pimvc\Http\Response::TYPE_JSON)
            ->setHttpCode(200);
    }

}
