<?php

/**
 * Description of interface requestInterface
 *
 * @author pierrefromager
 */

namespace lib\http\interfaces;

interface requestInterface {
    
    const REQUEST_METHOD = 'REQUEST_METHOD';
    const REQUEST_METHOD_GET = 'GET';
    const REQUEST_METHOD_POST = 'POST';
    const REQUEST_P_METHOD = 'method';
    const REQUEST_P_REQUEST = 'request';
    const REQUEST_P_COOKIE = 'cookie';
    const REQUEST_URI = 'REQUEST_URI';
    const REQUEST_QUERY = 'query';
    
    public function getMethod();

    public function isHome();

    public function getUri();

    public function getServer($param);

    public function getParsedQuery($query);

    public function getParsedQueryTupple($query);
    
    public function getQueryTupple($query);
    
    public function get();
}