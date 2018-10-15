<?php
/**
 * Description of Pimvc\Views\Helpers\Bootstrap\Flash
 *
 * @author pierrefromager
 */
namespace Pimvc\Views\Helpers\Bootstrap;

use \Pimvc\Views\Helpers\Glyph as glyph;
use Pimvc\Html\Element\Decorator as deco;

class Flash
{

    const A = 'a';
    const DIV = 'div';
    const P = 'p';
    const _CLASS = 'class';
    const SUCCESS = 'success';
    const INFO = 'info';
    const WARNING = 'warning';
    const DANGER = 'danger';
    const ERROR = 'error';
    const DATA_DISMISS = 'data-dismiss';
    const ALERT_DISMISS = 'alert-dismissible';
    const TITLE = 'title';
    const CLOSE = 'close';
    const ARIA_LABEL = 'aria-label';
    const HREF = 'href';
    const BS_CLASS_SEP = '-';
    const BS_ALTER = 'alert';
    const BS_DESCR = ' : ';
    const DASH = '#';
    const _BUTTON = 'button';
    const _CLOSE_X = '<small>x</small>';
    const _ID = 'id';
    const _TYPE = 'type';

    /**
     * get
     *
     * @param array $flashes
     * @return string
     */
    public static function get($flashes)
    {
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
    private static function getFlash($type, $message)
    {
        $bsClass = self::getMappedBsClass($type);
        $classes = self::BS_ALTER . ' ' . $bsClass . ' ' . self::ALERT_DISMISS;
        $text = self::getIco($type) . ucfirst($type)
            . self::BS_DESCR . ucfirst($message);
        $closeLink = new deco(
            self::_BUTTON,
            self::_CLOSE_X,
            [
            self::_CLASS => self::CLOSE,
            self::_ID => md5($type . $message)
            , self::_TYPE => self::_BUTTON
            , self::DATA_DISMISS => self::BS_ALTER
            , self::ARIA_LABEL => self::CLOSE
            , self::TITLE => self::CLOSE
            ]
        );
        $flashMessage = (string) new deco(
            self::P,
            $text,
            []
        );
        return (string) new deco(
            self::DIV,
            $closeLink . $flashMessage,
            [
            self::_CLASS => $classes
            , 'role' => 'alert'
            ]
        );
    }

    /**
     * getMappedBsClass
     *
     * @param string $type
     * @return string
     */
    private static function getMappedBsClass($type)
    {
        $type = trim($type);
        $alertPrefix = self::BS_ALTER . self::BS_CLASS_SEP;
        $defaultClass = $alertPrefix . self::INFO;
        $mappedBsClass = array(
            self::INFO => $defaultClass
            , self::SUCCESS => $alertPrefix . self::SUCCESS
            , self::WARNING => $alertPrefix . self::WARNING
            , self::DANGER => $alertPrefix . self::DANGER
            , self::ERROR => $alertPrefix . self::DANGER
        );
        $isMapped = (isset($mappedBsClass[$type]));
        return ($isMapped) ? $mappedBsClass[$type] : $defaultClass;
    }

    /**
     * getIco
     *
     * @param string $type
     * @return string
     */
    private static function getIco($type)
    {
        $defaultIcon = glyph::get(glyph::INFO_SIGN);
        $icoTypes = array(
            self::INFO => $defaultIcon
            , self::SUCCESS => glyph::get(glyph::OK)
            , self::WARNING => glyph::get(glyph::WARNING_SIGN)
            , self::DANGER => glyph::get(glyph::THUMBS_DOWN)
            , self::ERROR => glyph::get(glyph::THUMBS_DOWN)
        );
        return (isset($icoTypes[$type])) ? $icoTypes[$type] : $defaultIcon;
    }
}
