<?php

/**
 * Description of interface routerInterface
 *
 * @author pierrefromager
 */

namespace pimvc\http\interfaces;

interface router {

    const URI_SEPARATOR = '/';
    const REQUEST_URI = 'REQUEST_URI';

    public function __construct(\pimvc\http\routes $routes);

    public function getUri();

    public function getFragments();

    public function compile();
}
