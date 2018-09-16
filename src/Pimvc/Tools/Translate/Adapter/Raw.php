<?php
/**
 * Description of Pimvc\Tools\Translate\Adapter\Csv
 *
 */

namespace Pimvc\Tools\Translate\Adapter;

use Pimvc\Tools\Lang as langTools;

class Raw
{
    const NEED_TRANSLATION_MESSAGE = '(translation required)';
    const LANG_SRC = 'langsrc';
    const LANG_DST = 'langdst';
    const LANG_COMMENT = 'comment';

    private $_locale;

    /**
     * @see __construct
     *
     * @param string $locale
     */
    public function __construct($locale)
    {
        $this->_locale = $locale;
    }

    /**
     * getTranslationData
     *
     * @return array
     */
    public function getTranslationData()
    {
        $data = langTools::getData($this->_locale);
        if (!$data) {
            return [];
        }
        $cleanedData = [];
        foreach ($data as $row) {
            $cleanedData[$row[self::LANG_SRC]] = $row[self::LANG_DST];
        }
        return $cleanedData;
    }

    /**
     * addTranslationItem
     *
     * @param string $msg
     */
    public function addTranslationItem($msg)
    {
        $data = langTools::getData($this->_locale);
        $data[] = [
            self::LANG_SRC => $msg
            , self::LANG_DST => $msg
            , self::LANG_COMMENT => self::NEED_TRANSLATION_MESSAGE
        ];
        langTools::import($this->_locale, $data);
    }

    /**
     * getLocale
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->_locale;
    }
}
