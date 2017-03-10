<?php

/**
 * Description of error
 *
 * @author Pierre Fromager
 */
namespace app1\controller;

class error extends \pimvc\controller\basic{
    
    const VIEW_FILE = '/views/error/index.php';

    /**
     * index
     * 
     * @return array
     */
    public function index() {
        return $this->getErrorView(
            $this->getErrorParams() , 
            $this->getApp()->getPath() . self::VIEW_FILE
        );
    }

    /**
     * getErrorView
     * 
     * @param array $params
     * @param string $filename
     * @return pimvc\http\response
     */
    private function getErrorView($params, $filename) {
        $view = $this->getApp()->getView();
        $view->setParams($params)->setFilename($filename)->render();
        return $this->getApp()->getResponse()->setContent($view)
            ->setType(\pimvc\http\response::TYPE_HTML)
            ->setHttpCode(200);
    }
    
    /**
     * getErrorParams
     * 
     * @return array
     */
    private function getErrorParams() {
        return [
            'errors' => $this->getParams('errors') ,
            'controller' => $this->getParams('controller') ,
            'action' => $this->getParams('action') ,
            'request' => $this->getParams('request') ,
        ];
    }
}