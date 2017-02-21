<?php

/**
 * Description of user controller
 *
 * @author Pierre Fromager
 */

namespace controller;

class user extends \lib\controller\basic{
    
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
        $idThreshold = (isset($params['id'])) ? (int) $params['id'] : 800;
        $emailContain = (isset($params['email'])) 
            ? '%' . $params['email'] . '%' 
            : '%';
        return $this->asJson(
            $this->userModel->find(
                ['id', 'email'] , 
                ['id#>' => $idThreshold, 'email' => $emailContain]
            )->getRowset()
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
