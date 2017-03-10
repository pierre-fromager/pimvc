<?php

/**
 * Description of pimvc\interfaces\layout
 *
 * @author pierrefromager
 */

namespace pimvc\interfaces;

interface layout {

    const LAYOUT_PATH = '/public/layouts/';
    const LAYOUT_EXT = '.html';
    const LAYOUT_DEFAULT_NAME = 'reponsive';

    public function setName($name);

    public function setLayoutParams($params = []);

    public function getLayoutParams();

    public function build();
}
