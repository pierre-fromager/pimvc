<?php

/**
 * Description of lib\interfaces\layout
 *
 * @author pierrefromager
 */

namespace lib\interfaces;

interface layout {

    const LAYOUT_PATH = '/public/layouts/';
    const LAYOUT_EXT = '.html';
    const LAYOUT_DEFAULT_NAME = 'reponsive';

    public function setName($name);

    public function setLayoutParams($params = []);

    public function getLayoutParams();

    public function build();
}
