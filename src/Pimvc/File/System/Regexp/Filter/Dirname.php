<?php
/**
 * Description of Pimvc\File\System\Regexp\Filter\Dirname;
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */

namespace Pimvc\File\System\Regexp\Filter;

use Pimvc\File\System\Regexp\Filter as fsFilter;

class Dirname extends fsFilter
{
    
    /**
     * accept
     *
     * @return boolean
     */
    public function accept()
    {
        return (!$this->isDir() || preg_match($this->regexp, $this->getFilename()));
    }
}
