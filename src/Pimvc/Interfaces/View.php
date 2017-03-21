<?php

/**
 * Description of Pimvc\Interfaces\View
 *
 * @author pierrefromager
 */

namespace Pimvc\Interfaces;

interface View {

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
