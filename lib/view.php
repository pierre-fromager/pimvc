<?php

namespace lib;

/**
 * view
 * 
 */
class view implements interfaces\view{
    
    private $content;
    private $params;
    private $filename;

    /**
     * __construct
     * 
     * @param array $params     
     * @param string $filename
     * @return $this
     */
    public function __construct() {
        return $this;
    }
    
    /**
     * setFilename
     * 
     * @param string $filename
     * @return $this
     */
    public function setFilename($filename) {
        $this->filename = $filename;
        if (!file_exists($filename)) {
            throw new \Exception(self::VIEW_ERROR_MISSING . $filename);
        }
        return $this;
    }
    
    /**
     * setParams
     * 
     * @param array $params
     * @return $this
     */
    public function setParams($params = []) {
        $this->params = $params;
        return $this;
    }
    
    /**
     * render
     * 
     */
    public function render() {
        ob_start();
        extract($this->params, EXTR_PREFIX_SAME, self::deserializer);
        include($this->filename);
        $this->content = ob_get_contents();
        ob_end_clean();
    }
    
    /**
     * getContent
     * 
     * @return string
     */
    public function getContent() {
        return $this->content;
    }
    
    /**
     * setContent
     * 
     * @return type
     */
    public function setContent($content) {
        $this->content = $content;
        return $this;
    }

    /**
     * __toString
     * 
     * @return string
     */
    public function __toString() {
        return (string) $this->getContent();
    } 
    
}
