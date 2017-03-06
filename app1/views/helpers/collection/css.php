<?php

/**
 * Description of app1\views\helpers\collection\css
 *
 * @author pierrefromager
 */

namespace app1\views\helpers\collection;

class css {

    const REGISTRY_KEY = 'Css';

    private static $helperCss;
    public static $items;
    private static $content;
    

    /**
     * add
     * 
     * @param \Helper_Css $itemHelperCss 
     */
    public static function add($cssHelper, $external = false) {
        self::init();   
        if (is_string($cssHelper)){
            $cssPath = $cssHelper;
            self::$items[$cssPath] = self::$helperCss->getNew($cssPath);
        } elseif (self::isHelperCssInstance($cssHelper)){
            self::$helperCss = $cssHelper;
            self::$items[$cssHelper] = $cssHelper;
        } elseif (is_array($cssHelper)) {
            foreach ($cssHelper as $pathItem) {
                if (is_string($pathItem)) {
                    self::$items[$pathItem] = self::$helperCss->getNew($pathItem);
                }             
            }
        }
        self::save();     
    }
    
    /**
     * save
     * 
     */
    public static function save() {
        \app1\app::getInstance()->storage->css = self::$items;
    }

    /**
     * render
     * 
     */
    public static function render() {
        $items = \app1\app::getInstance()->storage->css;
        if ($items) {
            foreach ($items as $item) {
                self::$content .= (self::isHelperCssInstance($item)) 
                    ? $item->render() . PHP_EOL 
                    : '';
            }
        }
        return self::$content;
    }
    
    /**
     * get
     * 
     * @param type $cssHelper
     * @return \Helper_Css | string 
     */
    public static function get($cssHelper) {
        self::init();
        if (is_string($cssHelper)){
            if (isset(self::$items[$cssHelper])) {
                return self::$items[$cssHelper];
            }
        } elseif (self::isHelperCssInstance($cssHelper)){
            return self::$items[$cssHelper->href];
        }        
    }

    /**
     * init
     * 
     */
    private static function init() {
        if (!self::isHelperCssInstance(self::$helperCss)){
            self::$helperCss = new \app1\views\helpers\css;
        }
    }

    /**
     * isHelperCssInstance
     * 
     * @param Helper_Css $obj
     * @return boolean 
     */
    private static function isHelperCssInstance($obj) {
        return $obj instanceof \app1\views\helpers\css;
    }

}

