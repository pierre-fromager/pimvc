<?php
/**
 * Description of Pimvc\Tools\Lang
 * 
 */

namespace Pimvc\Tools;

use Pimvc\File\Csv\Parser as csvParser;
use Pimvc\Tools\Translate\Adapter\Csv as csvTranslateAdapter;

class Lang {
    
    const CSV_EXT = '.csv';
    const CSV_PATH = '../public/lang';

    /**
     * import
     * 
     * @param string $lang
     * @param string $filenameOrData
     * @return boolean
     */
    public static function import($lang, $filenameOrData) {
        $lang = str_replace(
            DIRECTORY_SEPARATOR,
            '',
            filter_var($lang, FILTER_SANITIZE_STRING)
        );
        if (!$lang || mb_strlen($lang) != 2) {
            return false;
        }
        $unlinkafter = false;
        if (is_array($filenameOrData)) {
            $temp_file = tempnam(sys_get_temp_dir(), 'csv');
            $fp = fopen($temp_file, 'w');
            $headers = array_keys($filenameOrData[0]);
            fputcsv($fp, $headers);
            foreach ($filenameOrData as $fields) {
                fputcsv($fp, $fields);
            }
            fclose($fp);            
            $filenameOrData = $temp_file;
            $unlinkafter = true;
        }

        $csv = new csvParser();
        if ($this->isValidParsing($csv, $filenameOrData)) {
            $data = $csv->unparse($csv->data, array(), null, null, ',');
            if ($unlinkafter) {
                unlink($filenameOrData);
            }
            $fileUpdate = file_put_contents(self::getLangPath($lang), $data);
            return $fileUpdate;
        } else {
            return false;
        }
    }
    
    /**
     * isValidParsing
     * 
     * @param csvParser $csv
     * @param mixed $filenameOrData
     * @return boolean
     */
    private function isValidParsing(csvParser $csv,$filenameOrData) {
        $isValid =  (
            $csv->auto($filenameOrData) //parsed
            && count($csv->data) //hasdata
            && count($csv->data[0]) === 3 // column count match
            && isset($csv->data[0]['langsrc']) // has langsrc
            && isset($csv->data[0]['langdst']) // has langdst
            && isset($csv->data[0]['comment']) // has comment
        );
        return $isValid;
    }

    /**
     * export
     * 
     * @param string $lang
     * @return boolean
     */
    public static function export($lang) {
        $csv = new csvParser($lang);
        if (!$csv->auto(self::getLangPath($lang))) {
            return false;
        }
        $csv->output($lang . self::CSV_EXT);
        exit();
    }

    /**
     * getData
     * 
     * @param string $lang
     * @return boolean
     */
    public static function getData($lang) {
        $csv = new csvParser($lang);
        if (!$csv->auto(self::getLangPath($lang))) {
            return false;
        }
        return $csv->data;
    }
    
    /**
     * getLangPath
     * 
     * @param string $lang
     * @return string
     */
    private static function getLangPath($lang) {
        $lang = substr($lang,0,2);
        $appPath = \Pimvc\App::getInstance()->getPath();
        $langPath =  $appPath . self::CSV_PATH . DIRECTORY_SEPARATOR 
            . $lang . self::CSV_EXT;
        return $langPath;
    }
}

