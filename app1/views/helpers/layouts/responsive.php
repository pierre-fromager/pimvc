<?php

/**
 * Description of app1\views\helpers\layouts\responsive
 *
 * @author pierrefromager
 */

namespace app1\views\helpers\layouts;

class responsive extends \lib\layout {

    protected $path;
    protected $layoutParams = [];
    protected $app;
    protected $name;
    protected $config;

    /**
     * __construct
     * 
     * @param string $name
     * @return $this
     */
    public function __construct() {
        parent::__construct();
        return $this;
    }
    
    /**
     * setApp
     * 
     * @param type $app
     * @return $this
     */
    public function setApp(\lib\app $app) {
        $this->app = $app;
        $this->path = $this->app->getPath() . self::LAYOUT_PATH . DIRECTORY_SEPARATOR;
        $this->layoutConfig = $this->app->getConfig()->getSettings('html')['layout'];
        $this->htmlParts = $this->getHtmlParts();
        return $this;
    }

    /**
     * getLayoutParams
     * 
     * @return array
     */
    public function getLayoutParams() {
        return [
            'header' => [
                'doctype' => $this->layoutConfig['doctype'],
                'description' => $this->layoutConfig['description'], 
                'publisher' => $this->layoutConfig['publisher'],
                'revisitafter' => $this->layoutConfig['revisitafter'],
                'copyright' => $this->layoutConfig['copyright'],
                'author' => $this->layoutConfig['author'],
                'organization' => $this->layoutConfig['organization'],
                'keywords' => $this->layoutConfig['keywords'],
                'root_url' => '', //BASE_URI,
                'baseurl' => $this->app->getRequest()->getBaseUrl() . '/app1/',
                'title' => $this->layoutConfig['title'], 
            ], 'body' => [
                'request' => $this->app->getRequest(),
                'breadcrumb' => '', //Helper_Breadcrumb::get(),
                'langSelector' => '', //(string) new Helper_Lang(),
                'content' => (isset($this->layoutParams['content'])) 
                    ? (string) $this->layoutParams['content'] 
                    : '',
                'baseurl' => $this->app->getRequest()->getBaseUrl(), 
                'searchValue' => '', //$request->getParam('searchmotif'),
                'serviceMenu' => '', //(string) new Helper_Slicknavmenu(),
                'needPresence' => '', //($controllerName !== 'ulto'),
                'cloud' => '', //isset($frontValues['cloud']) ? $frontValues['cloud'] : '',
            ],
            'footer' => [
                'baseurl' => $this->app->getRequest()->getBaseUrl(),
                'copyright' => $this->layoutConfig['copyright'],
                'organization' => $this->layoutConfig['organization'],
                'street' => $this->layoutConfig['street'],
                'pocode' => $this->layoutConfig['pocode'],
                'city' => $this->layoutConfig['city'],
                'country' => $this->layoutConfig['country'],
                'email' => $this->layoutConfig['email'],
                'date' => date('d M Y H:i:s'),
                'ellapse' => '', //profiling::getEllpase('stop','start')
            ]
        ];
    }
}
