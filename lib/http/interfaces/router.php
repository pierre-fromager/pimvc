<?php

/**
 * Description of interface routerInterface
 *
 * @author pierrefromager
 */

namespace lib\http\interfaces;

interface routerInterface {

    const URI_SEPARATOR = '/';
    const REQUEST_URI = 'REQUEST_URI';

    public function __construct(\lib\http\routes $routes);

    public function getUri();

    public function getFragments();

    public function compile();
}
