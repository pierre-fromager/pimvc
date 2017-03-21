<?php

/**
 * Description of Helper_Css
 *
 * @author pierrefromager
 */

namespace App1\Views\Helpers;

use \Pimvc\Html\Element\Decorator;

class Css {

    const PARAM_TAG = 'link';
    const PARAM_CONTENT = 'content';
    const PARAM_BASEURL = 'baseUrl';
    const PARAM_SCREEN = 'screen';
    const PARAM_REL = 'stylesheet';
    const PARAM_TYPE = 'text/css';

    public $href;
    public $media;
    public $rel;
    public $type;
    private $content;
    private $baseUrl;

    /**
     * __construct
     * 
     * @return \Helper_Css 
     */
    public function __construct() {
        $this->media = self::PARAM_SCREEN;
        $this->rel = self::PARAM_REL;
        $this->type = self::PARAM_TYPE;
        $this->baseUrl = Tools_Session::getBaseUrl();
        return $this;
    }
    
    /**
     * getNew
     * 
     * @return \this 
     */
    public function getNew($href, $external = false) {
        $instance = new $this;
        $instance->setHref($href, $external);
        return $instance;
    }
    
    /**
     * setMedia
     * 
     * @param string $media
     * @return \Helper_Css 
     */
    public function setMedia($media) {
        $this->media = $media;
        return $this;
    }
    
    /**
     * setRel
     * 
     * @param string $rel
     * @return \Helper_Css 
     */
    public function setRel($rel) {
        $this->rel = $rel;
        return $this;
    }
    
    /**
     * setType
     * 
     * @param string $type
     * @return \Helper_Css 
     */
    public function setType($type) {
        $this->type = $type;
        return $this;
    }

    /**
     * setHref
     * 
     * @param type $href
     * @return \Helper_Css 
     */
    public function setHref($href, $external) {
        $this->href = ($external) ? $href : $this->baseUrl . $href;
        return $this;
    }
    
    /**
     * render
     * 
     */
    public function render() {
        $this->content = (string) new decorator(
            self::PARAM_TAG
            , ''
            , $this->getProperties()
        );
        return $this;
    }

    /**
     * renderElementOptions
     * 
     * @param string $fieldName
     * @return string 
     */
    private function getProperties() {
        $options = get_object_vars($this);
        unset($options[self::PARAM_CONTENT]);
        unset($options[self::PARAM_BASEURL]);
        return $options;
    }
    
    /**
     * __toString
     * 
     * @return string 
     */
    public function __toString() {
        return (string) $this->content;
    }
}
