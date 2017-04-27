<?php

/**
 * Lib_File_System_Indexer_Abstract
 *
 * @author pierrefromager
 */

namespace Pimvc\File\System\Indexer;

use Pimvc\File\System\Indexer\Filter as indexerFilter;

class _abstract {
    
    const INDEXER_PROCESSOR = 'egrep';
    const INDEXER_OPTIONS = ' -E ';
    const INDEXER_DEBUG = false;
        
    protected $filename;
    protected $search;
    public $filter;
    protected $results;
    protected $countResults;

    /**
     * __construct
     * 
     * @param string $filename 
     */
    public function __construct($filename) {
        $this->filename = $filename;
        $this->search = '';
        $this->results = [];
        $this->countResults = 0;
        $this->filter = new indexerFilter();
    }

    /**
     * run
     * 
     * @return int 
     */
    public function run() {
        $returnCode = 0;
        exec($this->search(), $this->results, $returnCode);
        $this->countResults = count($this->results);
        return $returnCode;
    }
    
    /**
     * getResults
     * 
     * @return array 
     */
    public function getResults() {
        return $this->results;
    }
    
    /**
     * count
     * 
     * @return integer 
     */
    public function count() {
        return $this->countResults;
    }


    /**
     * getHashedResults
     * 
     * @return array 
     */
    public function getHashedResults() {
        $callback = array($this, 'hashedResult');
        $hashesResult = array_map($callback, $this->getResults());
        return $hashesResult;
    }
    
    /**
     * hashedResult
     * 
     * @param string $value
     * @return stdClass 
     */
    private function hashedResult($value) {
        $hash = new stdClass();
        $hash->filename = $value;
        $hash->hash = md5($value);
        return $hash;
    }

    /**
     * __destruct
     * 
     */
    public function __destruct() {
        unset($this->filter);
    }
    
    /**
     * addFilter
     * 
     * @param string $name
     * @param string $value 
     */
    public function addFilter($name, $value, $order = '') {
       $this->filter->add($name, $value, $order = '');
    }
    
    /**
     * removeFilter
     * 
     * @param string $name 
     */
    public function removeFilter($name) {
       $this->filter->remove($name);
    }
    
    /**
     * search
     * 
     * @return string 
     */
    private function search() {
        $regex = $this->filter->getRegex();
        $this->search = self::INDEXER_PROCESSOR
            . self::INDEXER_OPTIONS
            . $regex
            . $this->filename;
        if (self::INDEXER_DEBUG) {
            echo $this->search;
        }
        return $this->search;
    }
}

