<?php

/**
 * Description of pimvc\file\system\regexp\filter
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */

namespace pimvc\file\system\regexp;

class filter extends \RecursiveRegexIterator {
    
    protected $regexp;
    
    /**
     * __construct
     *
     * @param \RecursiveIterator $it
     * @param string $regexp
     */
    public function __construct(\RecursiveIterator $it, $regexp) {
        $this->regexp = $regexp;
        $mode = \RegexIterator::GET_MATCH;
        parent::__construct($it, $regexp, $mode);
    }
}