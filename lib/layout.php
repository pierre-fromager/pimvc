<?php

/**
 * class Layout
 * is a layout manager
 * 
 * @author Pierre Fromager <pf@pier-infor.fr>
 */
namespace lib;

class layout {

    const PUBLIC_PATH = '/../public';
    const layoutPath = 'layout/';
    const LAYOUT_EXTENSION = '.html';

    protected $layoutContent = null;
    protected $layoutPath;
    protected $layoutName;
    protected $params;

    /**
     * __construct
     * 
     * @param string $name
     * @param array $params 
     */
    public function __construct($name, $params) {
        $this->layoutName = $name;
        $this->layoutPath = self::PUBLIC_PATH . DIRECTORY_SEPARATOR 
            . $this->layoutName . DIRECTORY_SEPARATOR;
        $this->params = $params;
        $this->render();
    }
    
    /**
     * render
     * 
     */
    public function render() {
        foreach ($this->params as $layoutName => $layoutValues) {
            $layoutFilename = $layoutName . self::LAYOUT_EXTENSION;
            if ($this->exist($layoutFilename)) {
                $this->layoutContent .= new Helper_Partial(
                    $layoutValues
                    , $layoutFilename
                    , $this->layoutPath
                );
            } else {
                echo '<p style="color:ref;font-weight:bolder">'
                    . 'Missing layout file ' 
                    . $layoutFilename 
                    . '</p>';
                die;
            }
        }
    }

    /**
     * exist
     * 
     * @param string $layoutFilename
     * @return boolean 
     */
    private function exist($layoutFilename) {
        $realLayoutFilepath = $this->getRealPath() . $layoutFilename;
        return (stream_resolve_include_path($realLayoutFilepath) !== false);
    }
    
    /**
     * getRealPath
     * 
     * @return string 
     */
    private function getRealPath() {
        return APP_PATH . '/public/' . $this->layoutName . DIRECTORY_SEPARATOR;
    }

    /**
     * @see __toString
     * 
     * @return string 
     */
    function __toString() {
        return (string) $this->layoutContent;
    }

}