<?php

/**
 * Description of user controller
 *
 * @author Pierre Fromager
 */

namespace controller;

use \lib\input\filter as inputFilter;
use \lib\input\custom\filters\range as inputRange;

class user extends \lib\controller\basic{
    
    const PARAM_ID = 'id';
    const PARAM_EMAIL = 'email';
    const PARAM_LOGIN = 'login';
    const PARAM_PASSWORD = 'password';
    const VIEW_USER_PATH = '/views/user/';
    const WILDCARD = '%';
    
    private $modelConfig;
    private $userModel;

    /**
     * init
     * 
     */
    protected function init() {
        $this->modelConfig = $this->getApp()->getConfig()->getSettings('dbPool');
        $this->userModel = new \model\users($this->modelConfig);
    }
    
    /**
     * user
     * 
     * @return \lib\http\response
     */
    public function index() {
        $input = $this->getIndexInputFilter();
        $transform = new \stdClass();
        $transform->filter = $input->get();
        $transform->data = $this->userModel->find(
            [self::PARAM_ID, self::PARAM_EMAIL] , 
            [
                self::PARAM_ID.'#>' => (isset($input->id)) ? $input->id : 800
                , self::PARAM_EMAIL => (isset($input->email)) 
                    ? self::WILDCARD . $input->email . self::WILDCARD 
                    : self::WILDCARD
            ]
        )->getRowset();
        unset($input);
        return $this->asJson($transform);
    }
    
    /**
     * getIndexInputFilter
     * 
     * @return inputFilter
     */
    private function getIndexInputFilter() {
        return new inputFilter(
            $this->getParams()
            , [
                self::PARAM_ID => new inputRange(
                    [
                        inputRange::MIN_RANGE => 1,
                        inputRange::MAX_RANGE => 10000,
                        inputRange::_DEFAULT => 800,
                        inputRange::CAST => inputRange::FILTER_INTEGER
                    ]
                ),
                self::PARAM_EMAIL => FILTER_SANITIZE_STRING
            ]
        );
    }
    
    /**
     * login
     * 
     * @return type
     */
    public function login() {
        $request = $this->getApp()->getRequest();
        $postedData = $request->get()[$request::REQUEST_P_REQUEST];
        $inputLoginFilter = $this->getLoginInputFilter($postedData);
        $form = (new \lib\form())
            ->setMethod($request::REQUEST_METHOD_POST)
            ->setAction($request->getUrl())
            ->setName('login-form')
            ->setFields([self::PARAM_LOGIN, self::PARAM_PASSWORD])
            ->setLabels([
                self::PARAM_LOGIN => 'Email', 
                self::PARAM_PASSWORD => 'Password'
            ])
            ->setDatas($inputLoginFilter->get())
            ->setRequest($request)
            ->get();
        return $this->getUserView(
            ['form' => (string) $form]
            , $this->getViewPath(__FUNCTION__)
        );
    }
    
    /**
     * getViewPath
     * 
     * @param string $method
     * @return string
     */
    private function getViewPath($method) {
        return $this->getApp()->getPath() 
            . self::VIEW_USER_PATH . $method . '.php';
    }

    /**
     * getLoginInputFilter
     * 
     * @return inputFilter
     */
    private function getLoginInputFilter($postedDatas) {
        return new inputFilter(
            $postedDatas
            , [
                self::PARAM_LOGIN => FILTER_SANITIZE_EMAIL,
                self::PARAM_PASSWORD => FILTER_SANITIZE_STRING
            ]
        );
    }
    
    /**
     * register
     * 
     * @return type
     */
    public function register() {
        $request = $this->getApp()->getRequest();
        $postedData = $request->get()[$request::REQUEST_P_REQUEST];
        $form = (new \lib\form())
            ->setMethod($request::REQUEST_METHOD_POST)
            ->setAction($request->getUrl())
            ->setName('register-form')
            ->setFields([self::PARAM_LOGIN, self::PARAM_PASSWORD])
            ->setLabels([
                self::PARAM_LOGIN => 'Login', 
                self::PARAM_PASSWORD => 'Password'
            ])
            ->setDatas($postedData)
            ->setRequest($request)
            ->get();
        return $this->getUserView(
            ['form' => (string) $form], 
            $this->getViewPath(__FUNCTION__)
        );
    }
    
    /**
     * po
     * 
     * @return \lib\http\response
     */
    public function po() {
        return $this->asJson($this->userModel->po());
    }

    /**
     * asJson
     * 
     * @param mixed $content
     * @return \lib\http\response
     */
    private function asJson($content) {
        return $this->getApp()->getResponse()
            ->setContent($content)
            ->setType(\lib\http\response::TYPE_JSON)
            ->setHttpCode(200);
    }
    
    /**
     * getUserView
     * 
     * @param array $params
     * @param string $filename
     * @return lib\http\response
     */
    private function getUserView($params, $filename) {
        $view = $this->getApp()->getView();
        $view->setParams($params)->setFilename($filename)->render();
        return $this->getApp()->getResponse()->setContent($view)
            ->setType(\lib\http\response::TYPE_HTML)
            ->setHttpCode(200)
            ->withCookie('lastVisitView' . md5($filename), time());
    }
}
