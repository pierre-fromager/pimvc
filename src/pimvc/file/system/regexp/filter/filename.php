<?php
/**
 * Lib_File_System_Regexp_Filter_Filename
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */

class Lib_File_System_Regexp_Filter_Filename extends Lib_File_System_Regexp_Filter {

    // Filter files against the regex
    public function accept() {
        return (!$this->isFile() || preg_match($this->regexp, $this->getFilename()));
    }

}

