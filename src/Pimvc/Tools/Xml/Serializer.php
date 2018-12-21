<?php

/**
 * Description of Pimvc\Tools\Xml\Serializer
 *
 * @author pierrefromager
 */
namespace Pimvc\Tools\Xml;

class Serializer
{

    const DEFAULT_ROOT_TAG = 'root';
    const DEFAULT_NODE_TAG = 'part';

    /**
     * generateValidXmlFromObj
     *
     * @param stdClass $obj
     * @param type $node_block
     * @param type $node_name
     * @return type
     */
    public static function generateValidXmlFromObj($obj, $node_block = '', $node_name = '')
    {
        $arr = get_object_vars($obj);
        $node_block = (empty($node_block)) ? self::DEFAULT_ROOT_TAG : $node_block;
        $node_name = (empty($node_name)) ? self::DEFAULT_NODE_TAG : $node_name;
        return self::stripInvalidXml(
            self::generateValidXmlFromArray(
                $arr,
                $node_block,
                $node_name
            )
        );
    }

    /**
     * generateValidXmlFromArray
     *
     * @param array $array
     * @param string $node_block
     * @param string $node_name
     * @return string
     */
    public static function generateValidXmlFromArray($array, $node_block = '', $node_name = '')
    {
        $xml = self::getHeader();
        $node_block = (empty($node_block)) ? self::DEFAULT_ROOT_TAG : $node_block;
        $node_name = (empty($node_name)) ? self::DEFAULT_NODE_TAG : $node_name;
        $xml .= self::getTag($node_block);
        $xml .= self::generateXmlFromArray($array, $node_name);
        $xml .= self::getTag($node_block, false);
        return $xml;
    }

    /**
     * getTag
     *
     * @param string $name
     * @param boolean $open
     * @return string
     */
    private static function getTag($name, $open = true)
    {
        $name = (substr($name, 0, 1) == '_') ? substr($name, 1) : $name;
        return ($open) ? '<' . $name . '>' : '</' . $name . '>';
    }

    /**
     * getHeader
     *
     * @param string $version
     * @param string $encoding
     * @return string
     */
    private static function getHeader($version = '', $encoding = '')
    {
        $version = (empty($version)) ? '1.0' : $version;
        $encoding = (empty($encoding)) ? 'UTF-8' : $encoding;
        return "<?xml version='" . $version . "' encoding='" . $encoding . "' ?>";
    }

    /**
     * generateXmlFromArray
     *
     * @param array $array
     * @param string $node_name
     * @return string
     */
    private static function generateXmlFromArray($array, $node_name)
    {
        $xml = '';
        $badChars = array('<', '>', '/');
        if (is_array($array) || is_object($array)) {
            foreach ($array as $key => $value) {
                if (is_numeric($key)) {
                    $key = $node_name;
                }
                $xml .= self::getTag($key)
                    . self::generateXmlFromArray($value, $node_name)
                    . self::getTag($key, false);
            }
        } else {
            $xml = htmlspecialchars(
                str_replace(
                    $badChars,
                    '',
                    $array
                ),
                ENT_QUOTES
            );
        }
        return $xml;
    }

    /**
     * Removes invalid XML
     *
     * @access public
     * @param string $value
     * @return string
     */
    private static function stripInvalidXml($value)
    {
        $ret = '';
        $current = '';
        if (empty($value)) {
            return $ret;
        }
        $length = strlen($value);
        for ($i = 0; $i < $length; $i++) {
            $current = ord($value{$i});
            if (($current == 0x9) ||
                ($current == 0xA) ||
                ($current == 0xD) ||
                (($current >= 0x20) && ($current <= 0xD7FF)) ||
                (($current >= 0xE000) && ($current <= 0xFFFD)) ||
                (($current >= 0x10000) && ($current <= 0x10FFFF))) {
                $ret .= chr($current);
            } else {
                $ret .= ' ';
            }
        }
        return $ret;
    }
}
