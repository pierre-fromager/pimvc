<?php
/**
 * Lib_File_System_Regexp_Filter_Filename
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */

namespace Pimvc\File\System\Regexp\Filter;

use Pimvc\File\System\Regexp\Filter as fsFilter;

class Filename extends fsFilter {

    /**
     * accept
     * 
     * @return boolean
     */
    public function accept() {
        return (!$this->isFile() || preg_match($this->regexp, $this->getFilename()));
    }

}

