<?php

/**
 * Description of nav
 *
 * @author pierrefromager
 */
namespace app1\views\helpers\bootstrap;

class nav extends \pimvc\view{
    
    public function __construct() {
        $this->setFilename(__DIR__ . '/template/' . 'nav.php' );
        parent::__construct();
        return $this;
    }
}
