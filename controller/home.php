<?php

/**
 * Description of home controller
 *
 * @author Pierre Fromager
 */

namespace controller;

use lib\controller\basicController;

class home extends basicController{
    
    /**
     * index
     * 
     * @return lib\http\response
     */
    public function index() {
        return $this->getHomeView(
            [
                'ip' => $this->getApp()->getRequest()->getServer('REMOTE_ADDR') ,
                'uri' => $this->getApp()->getRequest()->getServer('REQUEST_URI') ,
                'params' => $this->getParams()
            ]
            , $this->getApp()->getPath() .'/views/home/index.php'
        );
    }
    
    /**
     * getHomeView
     * 
     * @param array $params
     * @param string $filename
     * @return lib\http\response
     */
    private function getHomeView($params, $filename) {
        $view = $this->getApp()->getView();
        $view->setParams($params)->setFilename($filename)->render();
        return $this->getApp()->getResponse()->setContent($view)
            ->setType(\lib\http\response::TYPE_HTML)
            ->setHttpCode(200);
    }
}
