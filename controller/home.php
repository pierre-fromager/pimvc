<?php

/**
 * Description of home controller
 *
 * @author Pierre Fromager
 */

namespace controller;

class home extends \lib\controller\basic{
    
    /**
     * index
     * 
     * @return lib\http\response
     */
    public function index() {
        return $this->getHomeView(
            [
                'ip' => $this->getApp()->getRequest()->getServer('REMOTE_ADDR') ,
                'uri' => $this->getApp()->getRequest()->getUri() ,
                'params' => $this->getParams()
            ]
            , $this->getApp()->getPath() .'/views/home/index.php'
        );
    }
    
    /**
     * redir
     * 
     * @return lib\http\response
     */
    public function redir() {
        return $this->redirect('http://www.google.com');
    }
    
    /**
     * ping
     * 
     */
    public function ping() {
        $content = $this->forward(\controller\stat::class, 'pong', $this->getParams());
        return $this->asJson($content);
    }
    
    /**
     * json
     * 
     * @return lib\http\response
     */
    public function json() {
        $content = [
            'ns' => __NAMESPACE__ ,
            'class' => __CLASS__ ,
            'method' => __METHOD__ ,
            'params' => $this->getParams()
        ];
        return $this->asJson($content);
    }
    
    public function user() {
        $dbConfig = $this->getApp()->getConfig()->getSettings('dbPool');
        $users = new \model\users($dbConfig);
        echo '<pre>' . print_r($users,true) . '</pre>';die;
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
            ->setHttpCode(200)
            ->withCookie('lastVisitView' . md5($filename), time());
    }
}
