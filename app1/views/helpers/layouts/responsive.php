<?php

/**
 * Description of app1\views\helpers\layouts\responsive
 *
 * @author pierrefromager
 */

namespace app1\views\helpers\layouts;

class responsive extends \lib\view implements \lib\interfaces\view {

    const LAYOUT_PATH = '/public/layout_responsive/';
    const LAYOUT_EXT = '.html';

    protected $path;
    protected $layoutParams = [];
    protected $app;

    /**
     * __construct
     * 
     * @return $this
     */
    public function __construct() {
        $this->path = APP_PATH . self::LAYOUT_PATH;
        $this->app = \app1\app::getInstance();
        $this->htmlParts = $this->getHtmlParts();
        parent::__construct();
        return $this;
    }
    
    /**
     * setLayoutParams
     * 
     * @param array $params
     * @return $this
     */
    public function setLayoutParams($params = []) {
       $this->layoutParams = $params;
       return $this;
    }
    
    /**
     * getLayoutParams
     * 
     * @return array
     */
    private function getLayoutParams() {
        return [
            'header' => [
                'doctype' => '', //(string) new Helper_Doctype($config->application->site->doctype),
                'serverName' => '', //SERVER_NAME . BASE_URI,
                'rssMeta' => '', //$rssMeta,
                'description' => '', //$config->application->site->description,
                'publisher' => '', //$config->application->site->publisher,
                'revisitafter' => '', //$config->application->site->revisitafter,
                'copyright' => '', //$copyright,
                'author' => '', //$config->application->site->author,
                'organization' => '', //$config->application->site->organization,
                'keywords' => '', //$keywords,
                'root_url' => '', //BASE_URI,
                'baseurl' => $this->app->getRequest()->getBaseUrl() . '/app1/', //Tools_Session::get('baseurl'),
                'title' => '', //$config->application->site->title,
                'jqChartScript' => '', //Tools_Chart_Jqplot::loadPlugins($nline = true)
            ], 'body' => [
                'request' => '', //$request,
                'breadcrumb' => '', //Helper_Breadcrumb::get(),
                'langSelector' => '', //(string) new Helper_Lang(),
                'content' => (isset($this->layoutParams['content'])) 
                    ? (string) $this->layoutParams['content'] 
                    : '',
                'baseurl' => '', //$request->getBaseUrl(),                    
                'searchValue' => '', //$request->getParam('searchmotif'),
                'serviceMenu' => '', //(string) new Helper_Slicknavmenu(),
                'needPresence' => '', //($controllerName !== 'ulto'),
                'cloud' => '', //isset($frontValues['cloud']) ? $frontValues['cloud'] : '',
            ],
            'footer' => [
                'baseurl' => '', //Tools_Session::getBaseUrl()
                'copyright' => '', //$copyright
                'organization' => '', //$config->application->site->organization
                'street' => '', //$config->application->site->street
                'pocode' => '', //$config->application->site->pocode
                'city' => '', //$config->application->site->city
                'country' => '', //$config->application->site->country
                'email' => '', //$config->application->site->email
                'date' => date('d M Y H:i:s'),
                'ellapse' => '', //profiling::getEllpase('stop','start')
            ]
        ];
    }

    /**
     * build
     * 
     */
    public function build() {
        $content = '';
        foreach ($this->htmlParts as $part) {
            $filename = $this->path . $part . self::LAYOUT_EXT;
            $this->setParams($this->getLayoutParams()[$part]);
            $this->setFilename($filename);
            $this->render();
            $content .= $this->getContent();
        }
        $this->setContent($content);
    }

    /**
     * getHtmlParts
     * 
     * @return type
     */
    private function getHtmlParts() {
        return array_keys($this->getLayoutParams());
    }

}
