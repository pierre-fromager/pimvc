<?php

/**
 * Description of Pimvc\Views\Helpers\Urlselector
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */

namespace Pimvc\Views\Helpers;

use Pimvc\Html\Element\Decorator;

class Urlselector
{
    const OPTION_OPEN_VALUE = '<option value="';
    const OPTION_END = '">';
    const OPTION_CLOSE_VALUE = '</option>';
    const SELECT_OPEN_VALUE = '<select class="col-lg-6 form-control" name="';
    const SELECT_CLOSE_VALUE = '</select>';
    const OPTION_SELECTED = '" selected >';
    const OPTION_DEFAULT_MESSAGE = '- Selectionner -';
    const CR = "\n";
    
    private static $selected = null;

    /**
     * get
     *
     * @param string $name
     * @param string $url
     * @param array $items
     * @param string $default
     * @param string $defaultMessage
     * @return string
     */
    public static function get($name, $url, $items, $defaultValue, $defaultMessage = '')
    {
        self::$selected = $defaultValue;
        $selectorOptionsOptions = self::getOptions($items, $defaultValue, $defaultMessage);
        $selector = new Decorator('select', $selectorOptionsOptions, [
            'class' => 'col-lg-6 form-control',
            'name' => $name,
            'onchange' => 'location.href=\'' . $url . '\' + this.options[this.selectedIndex].value;'
        ]);
        return $selector;
    }
    
    /**
     * getOptions
     *
     * @param array $items
     * @param string $default
     * @return string
     */
    private static function getOptions($items, $defaultValue, $defaultMessage)
    {
        $optionsContent = self::getOption(
            $defaultMessage,
            $defaultValue,
            ($defaultValue == self::$selected)
        );
        $selectorOptionsItems = self::getTupple($items);
        foreach ($selectorOptionsItems as $key => $value) {
            $optionsContent .= self::getOption(
                $key,
                $value,
                ($value == self::$selected)
            );
        }
        return $optionsContent;
    }

    /**
     * getOption
     *
     * @param string $key
     * @param string $value
     * @param bool $selected
     * @return Decorator
     */
    private static function getOption(string $key, string $value, bool $selected): Decorator
    {
        $decoratorOptions = ['name' => $key, 'value' => $value];
        if ($selected) {
            $decoratorOptions['selected'] = 'selected';
        }
        return new Decorator('option', $value, $decoratorOptions);
    }

    /**
     * isAssoc
     *
     * @param array $array
     * @return boolean
     */
    private static function isAssoc($array)
    {
        $array = array_keys($array);
        return ($array !== array_keys($array));
    }
    
    /**
     * getTupple
     *
     * @param array $options
     * @return boolean
     */
    private static function getTupple($options)
    {
        $tupple = [];
        if (!self::isAssoc($options)) {
            foreach ($options as $value) {
                $tupple[$value] = $value;
            }
        } else {
            $tupple = $options;
        }
        return $tupple;
    }
}
