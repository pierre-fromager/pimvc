<?php

/**
 * Description of interface routerInterface
 *
 * @author pierrefromager
 */

namespace pimvc\http\interfaces;

interface routes {

    const URI_SEPARATOR = '/';
    const REQUEST_URI = 'REQUEST_URI';
    const BAD_ROUTE_COLLECTION = 'Bad routes collection';

    public function __construct($routes);

    public function getRoutes();

    public function setRoutes($routes);
}
