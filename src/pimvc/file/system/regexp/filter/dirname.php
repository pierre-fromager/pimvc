<?php
/**
 * Lib_File_System_Regexp_Filter_Dirname
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */

class Lib_File_System_Regexp_Filter_Dirname extends Lib_File_System_Regexp_Filter {
    // Filter directories against the regex
    
    public function accept() {
        return ( ! $this->isDir() || preg_match($this->regexp, $this->getFilename()));
    } 
}

