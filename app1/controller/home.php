<?php

/**
 * Description of home controller
 *
 * @author Pierre Fromager
 */

namespace app1\controller;

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
    
    public function layout() {
        $nav = new \app1\views\helpers\bootstrap\nav();
        $nav->setParams(['items' => [ 
            [
                'title' => '1st title'
                , 'icon' => 'fa fa-cutlery'
                , 'link' => 'http://www.google.com'
            ],
            [
                'title' => '2nd title'
                , 'icon' => 'fa fa-smile'
                , 'link' => 'http://www.google.com'
            ]
        ]]);
        $nav->render();
        $layout = (new \app1\views\helpers\layouts\responsive());
        $layout->setLayoutParams(
            ['head' => '', 'content' => (string) $nav, 'footer' => '']
        )->build();
        echo (string) $layout;
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
