<?php

/**
 * Description of interface RequestInterface
 *
 * @author pierrefromager
 */

namespace Pimvc\Http\Interfaces;

interface Request {

    const REQUEST_SAPI_CLI = 'cli';
    const REQUEST_METHOD = 'REQUEST_METHOD';
    const REQUEST_METHOD_GET = 'GET';
    const REQUEST_METHOD_POST = 'POST';
    const REQUEST_METHOD_PUT = 'PUT';
    const REQUEST_METHOD_PATCH = 'PATCH';
    const REQUEST_METHOD_HEAD = 'HEAD';
    const REQUEST_METHOD_OPTIONS = 'OPTIONS';
    const REQUEST_METHOD_COPY = 'COPY';
    const REQUEST_METHOD_LINK = 'LINK';
    const REQUEST_METHOD_VIEW = 'VIEW';
    const REQUEST_METHOD_PURGE = 'PURGE';
    const REQUEST_METHOD_UNLINK = 'UNLINK';
    const REQUEST_METHOD_LOCK = 'LOCK';
    const REQUEST_METHOD_UNLOCK = 'UNLOCK';
    const REQUEST_METHOD_PROPFIND = 'PROPFIND';
    const REQUEST_METHOD_DELETE = 'DELETE';
    const REQUEST_METHOD_TRACE = 'TRACE';
    const REQUEST_CONTENT_LENGTH = 'CONTENT_LENGTH';
    const REQUEST_CONTENT_TYPE = 'CONTENT_TYPE';
    const REQUEST_INPUT = 'php://input';
    const REQUEST_P_METHOD = 'method';
    const REQUEST_P_REQUEST = 'request';
    const REQUEST_P_COOKIE = 'cookie';
    const REQUEST_URI = 'REQUEST_URI';
    const REQUEST_QUERY = 'query';
    const REQUEST_HOST = 'HTTP_HOST';
    const REQUEST_SCHEME = 'REQUEST_SCHEME';
    const REQUEST_SLASH = '/';
    const SCHEME_SUFFIX = '://';
    const PARAM_APACHE_HEADERS = 'apache_request_headers';
    const PARAM_X_FORWARD = 'X-Forwarded-For';
    const PARAM_HTTP_X_FORWARD = 'HTTP_X_FORWARDED_FOR';
    const PARAM_SERVER_REMOTE_ADDR = 'REMOTE_ADDR';
    const REQUEST_HEADER_PREFIX = 'HTTP_';
    const REQUEST_HEADER_REDIRECT_PREFIX = 'REDIRECT_HTTP_';
    const REQUEST_HEADER_SPLITTER = '_';
    const REQUEST_HEADER_SEPARATOR = '-';    
    const HEADER_CONTENT_TYPE = 'Content-Type';
    const HEADER_CONTENT_TYPE_JSON = 'application/json';

    public function __construct(\Pimvc\Config $config);

    public function getMethod();
    
    public function setMethod($method);
    
    public function setOptions(\Pimvc\Config $config);

    public function isHome();

    public function getUri();
    
    public function setUri($uri);

    public function getHost();

    public function getUrl();
    
    public function getBaseUrl();

    public function getScheme();

    public function getServer($param);

    public function getParsedQuery($query);

    public function getQueryTupple($query);

    public function get();
    
    public function getParams();

    public function startSession();

    public function setSession($name, $value, $key = '');

    public function deleteSession($name, $key = '');

    public function hasSession($name, $key = '');

    public function getSession($name, $key = '');
    
    public function getHeaders();
}