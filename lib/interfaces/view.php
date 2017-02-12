<?php

/**
 * Description of lib\interfaces\view
 *
 * @author pierrefromager
 */

namespace lib\interfaces;

interface view {

    const deserializer = 'wddx';
    const VIEW_ERROR_MISSING = 'View filename not found : ';

    public function __construct();

    public function setFilename($filename);

    public function setParams();

    public function render();

    public function __toString();
}
