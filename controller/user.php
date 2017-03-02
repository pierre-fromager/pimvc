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
                'id#>' => (isset($input->id)) ? $input->id : 800
                , self::PARAM_EMAIL => (isset($input->email)) 
                    ? '%' . $input->email . '%' 
                    : '%'
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
    
    public function register() {
        $request = $this->getApp()->getRequest();
        $postedData = $request->get()[$request::REQUEST_P_REQUEST];

        $form = (new \lib\form())->setMethod('POST')->setAction($request->getUrl())
            ->setName('register-form')
            ->setFields(['login', 'password'])
            ->setLabels(['login' => 'lolo', 'password' => 'papa'])
            ->setDatas($postedData)
            ->setRequest($request)
            ->get();
        $viewParams = [
            'form' => $form
        ];
        return $this->getUserView(
            $viewParams, 
            $this->getApp()->getPath() .'/views/user/register.php'
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
