<?php

/**
 * Description of Pimvc\File\System\Regexp\Filter
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */

namespace Pimvc\File\System\Regexp;

class Filter extends \RecursiveRegexIterator {
    
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