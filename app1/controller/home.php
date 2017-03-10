<?php

/**
 * Description of home controller
 *
 * @author Pierre Fromager
 */

namespace app1\controller;

class home extends \lib\controller\basic{
    
    const PARAM_HTML = 'html';
    const PARAM_NAV = 'nav';
    const PARAM_CAROUSEL = 'carousel';

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
     * layout
     * 
     * @return type
     */
    public function layout() {
        $htmlConfig = $this->getApp()->getConfig()->getSettings(self::PARAM_HTML);
        $nav = (new \app1\views\helpers\bootstrap\nav());
        $nav->setParams($htmlConfig[self::PARAM_NAV])->render();
        $carousel = (new \app1\views\helpers\bootstrap\carousel());
        $carousel->setParams($htmlConfig[self::PARAM_CAROUSEL])->render();
        $layout = (new \app1\views\helpers\layouts\responsive());
        $layoutParams = ['content' => (string) $nav . (string) $carousel];
        $layout->setApp($this->getApp())->setName('responsive')->setLayoutParams($layoutParams)->build();
        return (string) $layout;
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
