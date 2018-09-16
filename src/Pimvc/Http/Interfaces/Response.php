<?php

/**
 * Description of interface ResponseInterface
 *
 * @author pierrefromager
 */

namespace Pimvc\Http\Interfaces;

interface Response
{
    const HTML = 'html';
    const TYPE_HTML = 'text/html';
    const TYPE_JSON = 'application/json';
    const TYPE_XML = 'application/xml';
    const TYPE_TEXT = 'text/plain';
    const CONTENT_TYPE = 'Content-Type: ';
    const HTTP_1 = 'HTTP/1.0 ';
    const HEADER_CACHE_CONTROL = 'Cache-Control: no-cache, must-revalidate';
    const HEADER_CACHE_EXPIRE = 'Expires: Sat, 26 Jul 1997 05:00:00 GMT';
    const HEADER_LOCATION = 'Location: ';
    const HTTP_CODES = [
        200 => '200 OK',
        302 => '302 Redirect',
        400 => '400 Bad request',
        401 => '401 Unauthorized',
        403 => '403 Forbiden',
        404 => '404 Not Found',
        500 => '500 Server Error'
    ];

    public function __construct();
            
    public function setContent($content);

    public function setHttpCode($code);

    public function setType($type = null);

    public function withCookie($name = '', $value = '', $ttl = 3600, $path = '/', $domain = '', $secure = false, $httponly = true);

    public function removeHeaders();

    public function redirect($url);

    public function sendHeaders();

    public function dispatch();
}
