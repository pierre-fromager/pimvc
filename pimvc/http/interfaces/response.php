<?php

/**
 * Description of interface responseInterface
 *
 * @author pierrefromager
 */

namespace lib\http\interfaces;

interface response {
    
    const HTML = 'html';
    const TYPE_HTML = 'text/html';
    const TYPE_JSON = 'application/json';
    const TYPE_XML = 'application/xml';
    const CONTENT_TYPE = 'Content-Type: ';
    const HTTP_1 = 'HTTP/1.0 ';
    const HEADER_CACHE_CONTROL = 'Cache-Control: no-cache, must-revalidate';
    const HEADER_CACHE_EXPIRE = 'Expires: Sat, 26 Jul 1997 05:00:00 GMT';
    const HEADER_LOCATION = 'Location: ';

    public function __construct($content);
            
    public function setContent($content);

    public function setHttpCode($code = 200);

    public function setType($type = null);

    public function withCookie($name = '', $value = '', $ttl = 3600, $path = '/', $domain = '', $secure = false, $httponly = true);

    public function removeHeaders();

    public function redirect($url);

    public function sendHeaders();

    public function dispatch();
}