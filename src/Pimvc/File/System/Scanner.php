<?php

/**
 * Description of Pimvc\File\System\Scanner
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */
namespace Pimvc\File\System;

use Pimvc\File\System\Regexp\Filter\Filename as filterFilename;
use Pimvc\File\System\Regexp\Filter\Dirname as filterDirname;

class Scanner
{
    const FILE_REGEXP_BEGIN = '/\.(?:';
    const FILE_REGEXP_END = ')$/';
    
    const DIR_REGEXP_BEGIN = '/^(?';
    const DIR_REGEXP_END = ')/';
    const _SPLITTER = '|';
    const _EMPTY = '';
    const _NOT = '!';

    protected $path = self::_EMPTY;
    protected $dirsExclude = [];
    protected $filesAllowed = [];
    protected $showDir = true;
    
    private $dirsRexep = self::_EMPTY;
    private $filesRexep = self::_EMPTY;
    public $filesScanned = [];

    public function __construct(
        $path,
        $dirsExclude = [],
        $filesAllowed = [],
        $dirInclude = false,
        $showDir = true
    ) {
        $this->path = $path;
        $this->dirsExclude = $dirsExclude;
        $this->filesAllowed = $filesAllowed;
        $this->showDir = $showDir;
        if (!empty($this->dirsExclude)) {
            $inverter = ($dirInclude) ? self::_EMPTY : self::_NOT;
            $this->dirsRexep = self::DIR_REGEXP_BEGIN
                . $inverter
                . implode(self::_SPLITTER, $this->dirsExclude)
                . self::DIR_REGEXP_END;
        }

        if (!empty($this->filesAllowed)) {
            $this->filesRexep = self::FILE_REGEXP_BEGIN
                . implode(self::_SPLITTER, $this->filesAllowed)
                . self::FILE_REGEXP_END;
        }
    }

    /**
     * process
     *
     */
    public function process()
    {
        $result = new \RecursiveDirectoryIterator(
            $this->path,
            \RecursiveDirectoryIterator::SKIP_DOTS
        );

        if (!empty($this->dirsRexep)) {
            $result = new filterDirname($result, $this->dirsRexep);
        }

        if (!empty($this->filesRexep)) {
            $result = new filterFilename($result, $this->filesRexep);
        }

        $options = ($this->showDir)
            ? \RecursiveIteratorIterator::CHILD_FIRST
            : \RecursiveIteratorIterator::LEAVES_ONLY;
        
        $items = new \RecursiveIteratorIterator($result, $options);
        foreach ($items as $file) {
            $this->filesScanned[] = (string) $file;
        }
    }
}
