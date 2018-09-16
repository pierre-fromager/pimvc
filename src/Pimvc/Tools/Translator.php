<?php

/**
 * Description of Pimvc\Tools\Translator
 *
 * @author pierrefromager
 */

namespace Pimvc\Tools;

class Translator
{
    private $_adapter = null;
    private $_data = [];
    private $_locale = null;

    /**
     * __construct
     *
     * @param Object $adapter
     */
    public function __construct($adapter)
    {
        $this->_adapter = $adapter;
        $this->_data = $this->_adapter->getTranslationData();
        $this->_locale = $this->_adapter->getLocale();
    }

    /**
     * translate
     *
     * @param string $msg
     * @return string
     */
    public function translate($msg)
    {
        if (!isset($this->_data[$msg])) {
            $this->_adapter->addTranslationItem($msg);
            $this->_data[$msg] = '';
            return $msg;
        }
        if ($this->_data[$msg]) {
            return $this->_data[$msg];
        }
        return $msg;
    }
}
