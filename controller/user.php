<?php

/**
 * Description of user controller
 *
 * @author Pierre Fromager
 */

namespace controller;

use \lib\input\custom\filters\range as inputFilter;
use \lib\input\custom\filters\range as inputRange;

class user extends \lib\controller\basic{
    
    const PARAM_ID = 'id';
    
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
        $params = $this->getParams();
        $input = new inputFilter(
            $params
            , [
                self::PARAM_ID => new inputRange(
                    [
                        inputRange::MIN_RANGE => 1,
                        inputRange::MAX_RANGE => 10000,
                        inputRange::CAST => inputRange::FILTER_INTEGER
                    ]
                )
            ]
        );
        $transform = new \stdClass();
        $transform->filter = $input->process();
        //var_dump($transform);die;
        $idThreshold = (isset($input->id)) ? $input->id : 800;
        $emailContain = (isset($input->email)) 
            ? '%' . $input->email . '%' 
            : '%';
        $transform->errors = $input->getMessages();
        $transform->validity = $input->isValid();
        $transform->data = $this->userModel->find(
            ['id', 'email'] , 
            ['id#>' => $idThreshold, 'email' => $emailContain]
        )->getRowset();
        return $this->asJson($transform);
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
