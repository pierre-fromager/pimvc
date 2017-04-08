<?php

/**
 * Description of basicInterface
 *
 * @author Pierre Fromager
 */

namespace Pimvc\Controller\Interfaces;

use Pimvc\App;

interface Basic {

    const VIEW_PATH = '/Views/';

    /**
     * __construct
     * 
     * @param \Pimvc\App $app
     * @param array $params
     */
    public function __construct(App $app, $params);

    /**
     * getApp
     * 
     * @return app
     */
    public function getApp();

    /**
     * getParams
     * 
     * @return array
     */
    public function getParams($key = '');

    /**
     * redirect
     * 
     * @param string $url
     */
    public function redirect($url);

    /**
     * forward
     * 
     * @param string $controller
     * @param string $action
     * @param array $params
     */
    public function forward($controller = '', $action = '', $params = []);

    /**
     * hasValue
     * 
     * @param string $param
     */
    public function hasValue($param);
    
    /**
     * getView
     * 
     * @param array $params
     * @param string $viewPath
     */
    public function getView($params, $viewPath);
}
