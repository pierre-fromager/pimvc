<?php

/**
 * Description of interface RequestInterface
 *
 * @author pierrefromager
 */

namespace Pimvc\Http\Interfaces;

interface Request {

    const REQUEST_METHOD = 'REQUEST_METHOD';
    const REQUEST_METHOD_GET = 'GET';
    const REQUEST_METHOD_POST = 'POST';
    const REQUEST_P_METHOD = 'method';
    const REQUEST_P_REQUEST = 'request';
    const REQUEST_P_COOKIE = 'cookie';
    const REQUEST_URI = 'REQUEST_URI';
    const REQUEST_QUERY = 'query';
    const REQUEST_HOST = 'HTTP_HOST';
    const REQUEST_SCHEME = 'REQUEST_SCHEME';
    const SCHEME_SUFFIX = '://';
    
    const PARAM_APACHE_HEADERS = 'apache_request_headers';
    const PARAM_X_FORWARD = 'X-Forwarded-For';
    const PARAM_HTTP_X_FORWARD = 'HTTP_X_FORWARDED_FOR';
    const PARAM_SERVER_REMOTE_ADDR = 'REMOTE_ADDR';

    public function __construct();

    public function getMethod();

    public function isHome();

    public function getUri();

    public function getHost();

    public function getUrl();

    public function getScheme();

    public function getServer($param);

    public function getParsedQuery($query);

    public function getParsedQueryTupple($query);

    public function getQueryTupple($query);

    public function get();

    public function startSession();

    public function setSession($name, $value, $key = '');

    public function deleteSession($name, $key = '');

    public function hasSession($name, $key = '');

    public function getSession($name, $key = '');
}