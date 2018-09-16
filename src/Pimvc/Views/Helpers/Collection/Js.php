<?php

/**
 * Description of Helper_Collection_Js
 *
 * @author pierrefromager
 */

namespace Pimvc\Views\Helpers\Collection;

class Js
{
    const REGISTRY_KEY = 'Js';
   
    public static $items;
    private static $helperJs;
    private static $content;
    

    /**
     * add
     *
     * @param \Helper_Css $itemhelperJs
     */
    public static function add($jsHelper, $external = false)
    {
        self::init();
        if (is_string($jsHelper)) {
            $jsPath = $jsHelper;
            self::$items[$jsPath] = self::$helperJs->getNew($jsPath, $external);
        } elseif (self::ishelperJsInstance($jsHelper)) {
            self::$helperJs = $jsHelper;
            self::$items[$jsHelper] = $jsHelper;
        } elseif (is_array($jsHelper)) {
            foreach ($jsHelper as $pathItem) {
                if (is_string($pathItem)) {
                    self::$items[$pathItem] = self::$helperJs->getNew($pathItem, $external);
                }
            }
        }
        self::save();
    }
    
    /**
     * save
     *
     */
    public static function save()
    {
        \Pimvc\App::getInstance()->getStorage()->js = self::$items;
    }

    /**
     * render
     *
     */
    public static function render()
    {
        $items = \Pimvc\App::getInstance()->getStorage()->js;
        if ($items) {
            foreach ($items as $item) {
                self::$content .= (self::ishelperJsInstance($item))
                    ? $item->render() . PHP_EOL
                    : '';
            }
        }
        return self::$content;
    }
    
    /**
     * get
     *
     * @param type $jsHelper
     * @return \Helper_Css | string
     */
    public static function get($jsHelper)
    {
        self::init();
        if (is_string($jsHelper)) {
            if (isset(self::$items[$jsHelper])) {
                return self::$items[$jsHelper];
            }
        } elseif (self::ishelperJsInstance($jsHelper)) {
            return self::$items[$jsHelper->src];
        }
    }

    /**
     * init
     *
     */
    private static function init()
    {
        if (!self::ishelperJsInstance(self::$helperJs)) {
            self::$helperJs = new \Pimvc\Views\Helpers\Js();
        }
    }

    /**
     * ishelperJsInstance
     *
     * @param Helper_Js $obj
     * @return boolean
     */
    private static function ishelperJsInstance($obj)
    {
        return $obj instanceof \Pimvc\Views\Helpers\Js;
    }
}
