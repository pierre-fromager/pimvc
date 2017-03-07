<?php

/**
 * Description of carousel
 *
 * @author pierrefromager
 */

namespace app1\views\helpers\bootstrap;

class carousel extends \lib\view{
    
    public function __construct() {
        $this->setFilename(__DIR__ . '/template/' . 'carousel.php' );
        parent::__construct();
        return $this;
    }
}
