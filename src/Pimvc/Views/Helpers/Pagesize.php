<?php

/**
 * Pimvc\Views\Helpers\Pagesize
 *
 * @author pierrefromager
 */

namespace Pimvc\Views\Helpers;

class Pagesize
{
    protected static $pagesizes = [10, 25, 50, 75, 100, 250, 500];

    /**
     * get
     *
     * @param int $code
     * @return string
     */
    public static function get($code)
    {
        return self::$pagesizes[$code];
    }
    
    /**
     * getData
     *
     * @return array
     */
    public static function getData()
    {
        return self::$pagesizes;
    }
    
    /**
     * getCombo
     *
     * @param string $currentSize
     * @return string
     */
    public static function getCombo($url, $currentSize)
    {
        $pageSizes = array_combine(self::getData(), self::getData());
        $selector = Urlselector::get(
            'pageSize',
            $url,
            $pageSizes,
            $currentSize
        );
        $result = (string) $selector;
        return $result;
    }
}
