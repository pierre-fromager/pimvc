<?php
/**
 * Lib_File_System_Regexp_Filter
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */

class Lib_File_System_Regexp_Filter extends RecursiveRegexIterator {
    
    protected $regexp;
    
    public function __construct(RecursiveIterator $it, $regexp) {
        $this->regexp = $regexp;
        $mode = RegexIterator::GET_MATCH;
        parent::__construct($it, $regexp, $mode);
    }
}

