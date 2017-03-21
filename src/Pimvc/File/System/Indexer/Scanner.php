<?php

/**
 * Description of Pimvc\File\System\Indexer\Scanner
 *
 * @author pierrefromager
 */

namespace Pimvc\File\System\Indexer;

class Scanner {
    
    const SCANNER_CMD = 'find';
    const SCANNER_SPACE = ' ';
    const SCANNER_REDIRECT = '>';
    const SCANNER_OUPUT_NULL = '2>&1 &';
    const SCANNER_TYPE_OPTION = '-type';
    const SCANNER_TYPE_OPTION_FILE = 'f';
    const SCANNER_TYPE_OPTION_FOLDER = 'd';
    const SCANNER_INAME = '-iname';
    const SCANNER_QUOTE = '"';
    const SCANNER_OR = '-o';
    const SCANNER_AND = '-and';   
    const SCANNER_NOT = '-not';
    const SCANNER_PATH = '-path';
    const SCANNER_BRACE_O = '\(';
    const SCANNER_BRACE_C = '\)';
    const SCANNER_WILD_QUOTE_O = '"*';
    const SCANNER_WILD_QUOTE_C = '*"';
    //'"*' .$path.'*"' 
    const SCANNER_TYPE_OPTION_DEFAULT = self::SCANNER_TYPE_OPTION_FILE;
    
    protected $name;
    protected $where;
    protected $type;
    protected $inames;
    protected $excludes;
    protected $results;
    
    /**
     * __construct
     * 
     * @param string $where
     * @param string $type 
     */
    public function __construct($name, $where, $type = self::SCANNER_TYPE_OPTION_FILE) {
        $this->name = $name;
        $this->inames = array();
        $this->where = $where;
        $this->excludes = array();
        $this->setType($type);
    }
    
    /**
     * run
     * 
     * @return int 
     */
    public function run() {
        $returnCode = false;
        exec($this->find(), $this->results, $returnCode);
        return $returnCode;
    }
    
    /**
     * addIname
     * 
     * @param string $iname 
     */
    public function addIname($iname) {
        $this->inames[$iname] = $iname;
    }
    
    /**
     * addInames
     * 
     * @param array $inames 
     */
    public function addInames($inames) {
        $this->inames = array_merge(
            $this->inames
            , array_combine($inames,$inames)
        );
    }
    
    /**
     * removeIname
     * 
     * @param string $iname 
     */
    public function removeIname($iname) {
        if ($this->hasIname($iname)) {
            unset($this->inames[$iname]);
        }
    }
    
    /**
     * hasIname
     * 
     * @param string $iname
     * @return boolean 
     */
    public function hasIname($iname) {
        return isset($this->inames[$iname]);
    }
    
    /**
     * setType
     * 
     * @param string $type 
     */
    public function setType($type) {
        $this->type = $type;
    }
    
    /**
     * setExcludes
     * 
     * set exclude folders
     * 
     * @param array $excludes 
     */
    public function setExcludes($excludes) {
        $this->excludes = $excludes;
    }

    /**
     * getInames
     * 
     * @return string 
     */
    private function getInames() {
        return $this->renderInames();
    }
    
    /**
     * getIname
     * 
     * @param string $iname
     * @return string 
     */
    private function getIname($iname) {
        return self::SCANNER_SPACE 
            . self::SCANNER_INAME . self::SCANNER_SPACE 
            . self::SCANNER_QUOTE . $iname . self::SCANNER_QUOTE
            . self::SCANNER_SPACE;
    }
    
    /**
     * renderInames
     * 
     * @return string 
     */
    private function renderInames() {
        $inames = ($this->inames) 
            ? array_map(
                array($this, 'getIname')
                , array_values($this->inames)
             ) 
            : array();
        $render =  self::SCANNER_BRACE_O 
            . implode(self::SCANNER_OR, $inames) 
            . self::SCANNER_BRACE_C;
        return $render;
    }

    /**
     * find
     * 
     * @return string 
     */
    private function find() {
        $find = self::SCANNER_CMD 
            . self::SCANNER_SPACE . $this->where . self::SCANNER_SPACE
            . $this->getExcludes() . self::SCANNER_SPACE
            . self::SCANNER_AND 
            . self::SCANNER_SPACE . $this->getInames() . self::SCANNER_SPACE
            . self::SCANNER_TYPE_OPTION . self::SCANNER_SPACE
            . $this->type . self::SCANNER_SPACE
            . self::SCANNER_REDIRECT . self::SCANNER_SPACE
            . $this->name
            . self::SCANNER_SPACE . self::SCANNER_OUPUT_NULL;
        return $find;
    }
        
    /**
     * renderExclude
     * 
     * @return string 
     */
    private function renderExclude() {
        $callback = array($this,'getExcludePath');
        $render = array_map($callback, $this->excludes);
        $glue = self::SCANNER_SPACE . self::SCANNER_OR . self::SCANNER_SPACE;
        $renderer = implode($glue, $render);
        return '! \( ' . $renderer . ' \)';
    }
    
    /**
     * getExcludePath
     * 
     * @param string $path
     * @return string 
     */
    private static function getExcludePath($path) {
        return self::SCANNER_SPACE . self::SCANNER_PATH . self::SCANNER_SPACE 
            . self::SCANNER_WILD_QUOTE_O . $path . self::SCANNER_WILD_QUOTE_C
            . self::SCANNER_SPACE;
    }

    /**
     * getExcludes
     * 
     * @return string 
     */
    private function getExcludes() {
        return $this->renderExclude();
    }
}

