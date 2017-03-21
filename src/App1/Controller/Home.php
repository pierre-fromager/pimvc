<?php

/**
 * Description of home controller
 *
 * @author Pierre Fromager
 */

namespace App1\Controller;

class Home extends \Pimvc\Controller\Basic{
    
    const PARAM_HTML = 'html';
    const PARAM_NAV = 'nav';
    const PARAM_CAROUSEL = 'carousel';

    /**
     * index
     * 
     * @return Pimvc\Http\Response
     */
    public function index() {
        return $this->getHomeView(
            [
                'ip' => $this->getApp()->getRequest()->getServer('REMOTE_ADDR') ,
                'uri' => $this->getApp()->getRequest()->getUri() ,
                'params' => $this->getParams()
            ]
            , $this->getApp()->getPath() .'/Views/Home/Index.php'
        );
    }
    
    /**
     * layout
     * 
     * @return type
     */
    public function layout() {
        $htmlConfig = $this->getApp()->getConfig()->getSettings(self::PARAM_HTML);
        $nav = (new \App1\Views\Helpers\Bootstrap\Nav());
        $nav->setParams($htmlConfig[self::PARAM_NAV])->render();
        $carousel = (new \App1\Views\Helpers\Bootstrap\Carousel());
        $carousel->setParams($htmlConfig[self::PARAM_CAROUSEL])->render();
        $layout = (new \App1\Views\Helpers\Layouts\Responsive());
        $layoutParams = ['content' => (string) $nav . (string) $carousel];
        $layout->setApp($this->getApp())->setName('responsive')->setLayoutParams($layoutParams)->build();
        return (string) $layout;
    }

    /**
     * redir
     * 
     * @return Pimvc\Http\Response
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
     * @return Pimvc\Http\Response
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
     * @return \Pimvc\Http\Response
     */
    private function asJson($content) {
        return $this->getApp()->getResponse()
            ->setContent($content)
            ->setType(\Pimvc\Http\Response::TYPE_JSON)
            ->setHttpCode(200);
    }
    
    /**
     * getHomeView
     * 
     * @param array $params
     * @param string $filename
     * @return Pimvc\Http\Response
     */
    private function getHomeView($params, $filename) {
        $view = $this->getApp()->getView();
        $view->setParams($params)->setFilename($filename)->render();
        return $this->getApp()->getResponse()->setContent($view)
            ->setType(\Pimvc\Http\Response::TYPE_HTML)
            ->setHttpCode(200)
            ->withCookie('lastVisitView' . md5($filename), time());
    }
}
