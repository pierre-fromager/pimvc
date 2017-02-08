<?php

namespace lib\http;

/**
 * response
 * 
 */
class response {
    
    const HTML = 'html';
    const TYPE_HTML = 'text/html';
    const TYPE_JSON = 'application/json';
    const TYPE_XML = 'application/xml';
    const CONTENT_TYPE = 'Content-Type: ';
    const HTTP_1 = 'HTTP/1.0 ';
    const HEADER_CACHE_CONTROL = 'Cache-Control: no-cache, must-revalidate';
    const HEADER_CACHE_EXPIRE = 'Expires: Sat, 26 Jul 1997 05:00:00 GMT';


    private $content;
    private $type;
    private $headers;
    private $httpCode;
    private $httpCodes = [
        200 => '200 OK' ,
        404 => '404 Not Found' ,
    ];

    /**
     * __construct
     * 
     * @param array $content     
     * @param string $type
     * @return $this
     */
    public function __construct($content = []) {
        $this->setContent($content);
        return $this;
    }
    
    /**
     * setHeaders
     * 
     * @return $this
     */
    private function setHeaders() {
        $this->headers[] = self::HTTP_1 . $this->httpCodes[$this->httpCode];
        $this->headers[] = self::HEADER_CACHE_CONTROL; 
        $this->headers[] = self::HEADER_CACHE_EXPIRE;
        $this->headers[] = $this->getContentType($this->type);
        return $this;
    }
    
    /**
     * getContentType
     * 
     * @param string $type
     * @return string
     */
    private function getContentType($type) {
        return self::CONTENT_TYPE . $type;
    }

    /**
     * setContent
     * 
     * @param array $content
     * @return $this
     */
    public function setContent($content = []) {
        $this->content = $content;
        return $this;
    }
    
    public function setHttpCode($code = 200) {
        $this->httpCode = $code;
        return $this;
    }

    /**
     * setType
     * 
     * @param string $type
     * @return $this
     */
    public function setType($type = null) {
        $this->type = ($type) ? $type : self::HTML;
        return $this;
    }
    
    /**
     * withCookie
     * 
     * @param string $name
     * @param string $value
     * @param int $ttl
     * @param string $path
     * @param string $domain
     * @param boolean $secure
     * @param boolean $httponly
     */
    public function withCookie($name = '', $value = '', $ttl = 3600, $path = '/', $domain = '', $secure = false, $httponly = true) {
        if ($name) {
            setcookie($name, $value, time() + $ttl, $path, $domain, $secure, $httponly);
            $_COOKIE[$name] = $value;
        }
        return $this;
    }
    
    /**
     * removeHeahers
     * 
     * @return $this
     */
    public function removeHeahers() {
        header_remove();
        return $this;
    }

    /**
     * sendHeaders
     * 
     */
    public function sendHeaders() {
        $headersLength = count($this->headers);
        for ($i = 0; $i < $headersLength; $i++) {
            header($this->headers[$i]);
        }
    }

    /**
     * dispatch
     * 
     */
    public function dispatch() {
        $this->setHeaders()->sendHeaders();
        echo (string) $this->content;
    }

}