<?php

/**
 * Description of interface RouterInterface
 *
 * @author pierrefromager
 */

namespace Pimvc\Http\Interfaces;

interface Router {

    const URI_SEPARATOR = '/';
    const REQUEST_URI = 'REQUEST_URI';

    public function __construct(\Pimvc\Http\Routes $routes);

    public function getUri();

    public function getFragments();

    public function compile();
}
