<?php

/**
 * Description of Pimvc\Views\Helpers\Interfaces\Widget
 *
 * @author pierrefromager
 */

namespace Pimvc\Views\Helpers\Interfaces;

interface Widget
{
    const PARAM_CLASS = 'class';

    public function __construct();

    public function render();

    public function setTitleOptions($options);

    public function setTitleDecorator($decorator);

    public function setBodyOptions($options);

    public function setSectionDecorator($decorator);

    public function setHeaderDecorator($decorator);

    public function setBody($body);

    public function setTitle($title);

    public function setSectionOptions($options);

    public function __toString();
}
