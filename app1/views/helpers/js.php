<?php

/**
 * Description of app1\views\helpers\js
 *
 * @author pierrefromager
 */

namespace app1\views\helpers;

use \lib\html\element\decorator;

class js {

    const PARAM_TAG = 'script';
    const PARAM_SPACE = ' ';
    const PARAM_CONTENT = 'content';
    const PARAM_BASEURL = 'baseUrl';
    const PARAM_TYPE = 'text/javascript';

    public $src;
    public $type;
    private $content;
    private $baseUrl;

    /**
     * __construct
     * 
     * @return \Helper_Css 
     */
    public function __construct() {
        $this->type = self::PARAM_TYPE;
        $this->baseUrl = Tools_Session::getBaseUrl();
        return $this;
    }
    
    /**
     * getNew
     * 
     * @return \this 
     */
    public function getNew($src, $external = false) {
        $instance = new $this;
        $instance->setSrc($src, $external);
        return $instance;
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
     * setSrc
     * 
     * @param type $href
     * @return \Helper_Css 
     */
    public function setSrc($src, $external = false) {
        $this->src = ($external) ? $src : $this->baseUrl . $src;
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
