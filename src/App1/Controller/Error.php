<?php

/**
 * Description of error
 *
 * @author Pierre Fromager
 */
namespace App1\Controller;

use \Pimvc\Http\Response;

class Error extends \Pimvc\Controller\Basic{
    
    const VIEW_FILE = '/Views/Error/Index.php';

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
     * @return Pimvc\Http\Response
     */
    private function getErrorView($params, $filename) {
        $view = $this->getApp()->getView();
        $view->setParams($params)->setFilename($filename)->render();
        return $this->getApp()->getResponse()->setContent($view)
            ->setType(Response::TYPE_HTML)
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