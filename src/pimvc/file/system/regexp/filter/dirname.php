<?php
/**
 * Description of pimvc\file\system\regexp\filter\dirname;
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */

namespace pimvc\file\system\regexp\filter;

use pimvc\file\system\regexp\filter as fsFilter;

class dirname extends fsFilter {
    
    /**
     * accept
     * 
     * @return boolean
     */
    public function accept() {
        return (!$this->isDir() || preg_match($this->regexp, $this->getFilename()));
    } 
}

