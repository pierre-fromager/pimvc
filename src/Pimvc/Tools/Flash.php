<?php


/**
 * Description of Pimvc\Tools\Flash
 *
 * @author pierrefromager
 */
namespace Pimvc\Tools;

use Pimvc\Tools\Session as sessionTools;

class Flash {

    const FLASH_SESSION_KEY = 'flashdata';
    const FLASH_ERROR = 'error';
    const FLASH_WARNING = 'warning';
    const FLASH_INFO = 'info';
    const FLASH_HTTP_REDIRECT_CODE = 302;
    
    /**
     * addInfo
     * 
     * @param string $message 
     */
    public static function addInfo($message) {
        self::add(self::FLASH_INFO, $message);
    }
    
    /**
     * addWarning
     * 
     * @param string $message 
     */
    public static function addWarning($message) {
        self::add(self::FLASH_WARNING, $message);
    }

    /**
     * addError
     * 
     * @param string $message 
     */
    public static function addError($message) {
        self::add(self::FLASH_ERROR, $message);
    }

    /**
     * add
     * 
     * @param string $type
     * @param string $message 
     */
    public static function add($type, $message) {
        if (!empty($type) && !empty($message)) {
            $flash = new \stdClass();
            $flash->type = $type;
            $flash->message = $message;
            self::set($flash);
        }
    }
    
    /**
     * render
     * 
     * @return string 
     */
    public static function render($helperName = '\Pimvc\Views\Helpers\Bootstrap\Flash') {
        $flashes = self::get();
        $helperRender = '';
        if ($flashes) {
            $helperRender = $helperName::get($flashes);
            if (!self::isRedirect()) {
                self::clean();
            }
        }
        unset($flashes);
        return $helperRender;
    }

    /**
     * set
     * 
     * @param stdClass $flash 
     */
    private static function set($flash) {
        $flashes = array_merge(self::get(),array($flash));
        sessionTools::set(self::FLASH_SESSION_KEY, $flashes);
    }
    
    /**
     * clean
     * 
     */
    private static function clean() {
        if (self::has()) {
            sessionTools::set(self::FLASH_SESSION_KEY, array());
        }
    }

        /**
     * has
     * 
     * @return boolean 
     */
    private static function has() {
        return sessionTools::has(self::FLASH_SESSION_KEY);
    }
    
    /**
     * get
     * 
     * @return array 
     */
    private static function get() {
        return (self::has()) 
            ? sessionTools::get(self::FLASH_SESSION_KEY) 
            : array();
    }
    
    /**
     * isRedirect
     * 
     */
    private static function isRedirect() {
        $redirect = false;
        foreach(headers_list() as $header) {
            if ($header == 'Status:302 Found'){
                $redirect = true;break;
            };
        }
        return $redirect;
    }
}

