<?php

/**
 * Description of pimvc\interfaces\view
 *
 * @author pierrefromager
 */

namespace pimvc\interfaces;

interface view {

    const deserializer = 'wddx';
    const VIEW_ERROR_MISSING = 'View filename not found : ';

    public function __construct();

    public function setFilename($filename);

    public function setParams();

    public function render();
    
    public function getContent();
    
    public function setContent($content);

    public function __toString();
}
