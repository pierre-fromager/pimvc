<?php

/**
 * Description of Helper_Fonts
 *
 * @author pierrefromager
 */

namespace Pimvc\Helper;

use Pimvc\Html\Element\Decorator as glyphDecorator;

class Fonts
{
    const PARAM_CLASS = 'class';
    const PARAM_HREF = 'href';
    const GLYPH_TAG = 'span';
    const GLYPH_TAG_GLYPHICON_CLASS = 'glyphicon glyphicon-';
    const GLYPH_TAG_AWESOME_CLASS = 'fa fa-';
    const GLYPH_TAG_CONTENT = '&nbsp;';
    const LINK_DECORATOR = 'a';
    const DEFAULT_LINKED_CLASS = 'linkedIcon';
    const HELPER_GLYPH = 'Pimvc\Views\Helpers\Glyph';
    const HELPER_FA = 'Pimvc\Views\Helpers\Fa';
    const HELPER_CLASSES = [
      self::HELPER_GLYPH => self::GLYPH_TAG_GLYPHICON_CLASS,
      self::HELPER_FA => self::GLYPH_TAG_AWESOME_CLASS
    ];

    /**
     * get
     *
     * @param string $glyph
     * @param array $params
     * @param string $tag
     * @return string
     */
    public static function get($glyph, $params = [], $tag = self::GLYPH_TAG)
    {
        $defaultAttribs = array(
          self::PARAM_CLASS => self::getFontClass() . $glyph
        );
        $attribs = self::mergeArrayAttributes($defaultAttribs, $params);
        $html = new glyphDecorator(
            $tag,
            self::GLYPH_TAG_CONTENT,
            $attribs
        );
        $html->render();
        $glyphContent = (string) $html;
        unset($html);
        return $glyphContent;
    }

    /**
     * getLinked
     *
     * @param string $glyph
     * @param string $url
     * @param array $options
     * @return string
     */
    public static function getLinked($glyph, $url, $options = [])
    {
        $defaultAttribs = array(
          self::PARAM_CLASS => self::DEFAULT_LINKED_CLASS
          , self::PARAM_HREF => $url
        );
        $attribs = self::mergeArrayAttributes($defaultAttribs, $options);
        $linkDecorator = new glyphDecorator(
            self::LINK_DECORATOR,
            self::get($glyph),
            $attribs
        );
        $linkDecorator->render();
        $glyphLinkedContent = (string) $linkDecorator;
        unset($linkDecorator);
        return $glyphLinkedContent;
    }

    /**
     * getConstants
     *
     * @return array
     */
    public static function getConstants()
    {
        $rc = new ReflectionClass(get_called_class());
        $constants = array_reverse($rc->getConstants());
        // Forget 9 first local constants
        $constants = array_slice($constants, 9);
        ksort($constants);
        unset($rc);
        return $constants;
    }

    /**
     * mergeArrayAttributes
     *
     * @param array $attrArray1
     * @param array $attrArray2
     * @return array
     */
    private static function mergeArrayAttributes($attrArray1, $attrArray2)
    {
        return array_combine(
            array_merge(array_keys($attrArray1), array_keys($attrArray2)),
            array_merge(array_values($attrArray1), array_values($attrArray2))
        );
    }

    /**
     * getFontClass
     *
     */
    private static function getFontClass()
    {
        return self::HELPER_CLASSES[get_called_class()];
    }
}
