<?php
/**
 * Lib_File_System_Regexp_Filter_Filename
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */

namespace pimvc\file\system\regexp\filter;

use pimvc\file\system\regexp\filter as fsFilter;

class filename extends fsFilter {

    /**
     * accept
     * 
     * @return boolean
     */
    public function accept() {
        return (!$this->isFile() || preg_match($this->regexp, $this->getFilename()));
    }

}

