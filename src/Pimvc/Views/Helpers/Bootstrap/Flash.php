<?php

/**
 * Description of Pimvc\Views\Helpers\Bootstrap\Flash
 *
 * @author pierrefromager
 */

namespace Pimvc\Views\Helpers\Bootstrap;

use \Pimvc\Views\Helpers\Glyph as glyphHelper;

class Flash {

    const PARAM_A = 'a';
    const PARAM_DIV = 'div';
    const PARAM_P = 'p';
    const PARAM_CLASS = 'class';
    const PARAM_SUCCESS = 'success';
    const PARAM_INFO = 'info';
    const PARAM_WARNING = 'warning';
    const PARAM_DANGER = 'danger';
    const PARAM_ERROR = 'error';
    const PARAM_DATA_DISMISS = 'data-dismiss';
    const PARAM_TITLE = 'title';
    const PARAM_CLOSE = 'close';
    const PARAM_ARIA_LABEL = 'aria-label';
    const PARAM_HREF = 'href';
    const BS_CLASS_SEP = '-';
    const BS_ALTER = 'alert';
    const BS_DESCR = ' : ';
    const DASH = '#';
    
    /**
     * get
     * 
     * @param array $flashes
     * @return string 
     */
    public static function get($flashes) {
        $content = '';
        foreach ($flashes as $flash) {
            $content .= self::getFlash($flash->type, $flash->message);
        }
        return $content;
    }
        
    /**
     * getFlash
     * 
     * @param string $type
     * @param string $message
     * @return string 
     */
    private static function getFlash($type, $message) {
        $bsClass = self::getMappedBsClass($type);
        $classes = self::BS_ALTER . ' ' . $bsClass;
        $text = self::getIco($type) . ucfirst($type) 
            . self::BS_DESCR . ucfirst($message);
        $closeLink = new \Pimvc\Html\Element\Decorator(
            self::PARAM_A
            , '<small>' . glyphHelper::get(glyphHelper::eye_close) . '</small>'
            , array(
                self::PARAM_CLASS => self::PARAM_CLOSE
                , self::PARAM_HREF => self::DASH
                , self::PARAM_DATA_DISMISS => self::BS_ALTER
                , self::PARAM_ARIA_LABEL => self::PARAM_CLOSE
                , self::PARAM_TITLE => self::PARAM_CLOSE
            )
        );
        $flashMessage = (string) new \Pimvc\Html\Element\Decorator(
            self::PARAM_P
            , $text
            , []
        );
        return (string) new \Pimvc\Html\Element\Decorator(
            self::PARAM_DIV
            , $closeLink . $flashMessage
            , array(
                self::PARAM_CLASS => $classes 
            )
        );
    }
    
    /**
     * getMappedBsClass
     * 
     * @param string $type
     * @return string 
     */
    private static function getMappedBsClass($type) {
        $type= trim($type);
        $alertPrefix = self::BS_ALTER . self::BS_CLASS_SEP;
        $defaultClass = $alertPrefix . self::PARAM_INFO;
        $mappedBsClass = array(
            self::PARAM_INFO =>  $defaultClass
            , self::PARAM_SUCCESS => $alertPrefix . self::PARAM_SUCCESS
            , self::PARAM_WARNING => $alertPrefix . self::PARAM_WARNING
            , self::PARAM_DANGER => $alertPrefix . self::PARAM_DANGER
            , self::PARAM_ERROR => $alertPrefix . self::PARAM_DANGER
        );
        $isMapped = (isset($mappedBsClass[$type]));
        return ($isMapped) 
            ? $mappedBsClass[$type]
            : $defaultClass;
    }

    /**
     * getIco
     * 
     * @param string $type
     * @return string 
     */
    private static function getIco($type) {
        $defaultIcon = glyphHelper::get(glyphHelper::info_sign);
        $icoTypes = array(
            self::PARAM_INFO => $defaultIcon
            , self::PARAM_SUCCESS => glyphHelper::get(glyphHelper::ok)
            , self::PARAM_WARNING => glyphHelper::get(glyphHelper::warning_sign)
            , self::PARAM_DANGER => glyphHelper::get(glyphHelper::thumbs_down)
            , self::PARAM_ERROR => glyphHelper::get(glyphHelper::thumbs_down)
        );
        return (isset($icoTypes[$type])) 
            ? $icoTypes[$type] 
            : $defaultIcon;
    }
}

