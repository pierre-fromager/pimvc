<?php

/**
 * Description of lib\http\request
 *
 * @author Pierre Fromager
 */

namespace lib\http;

class request {

    const REQUEST_METHOD = 'REQUEST_METHOD';
    const REQUEST_METHOD_GET = 'GET';
    const REQUEST_METHOD_POST = 'POST';
    const REQUEST_P_METHOD = 'method';
    const REQUEST_P_REQUEST = 'request';
    const REQUEST_P_COOKIE = 'cookie';
    const REQUEST_URI = 'REQUEST_URI';

    private $request;
    private $method;
    private $cookie;
    private $server;

    /**
     * __construct
     * 
     * @return $this
     */
    public function __construct() {
        $this->assignServer()->assignMethod()->assignCookie();
        return $this;
    }
    
    /**
     * getMethod
     * 
     * @return string
     */
    public function getMethod() {
        return $this->method;
    }
    
    /**
     * isHome
     * 
     * @return boolean
     */
    public function isHome() {
        return ($this->getServer(self::REQUEST_URI) === DIRECTORY_SEPARATOR);
    }

    /**
     * getServer
     * 
     * @param string $param
     * @return array
     */
    public function getServer($param = '') {
        return ($param) ? $this->server[$param] : $this->server;
    }

    /**
     * get
     * 
     * @return array
     */
    public function get() {
        switch ($this->method) {
            case self::REQUEST_METHOD_GET: 
                $this->request = &$_GET;
                break;
            case self::REQUEST_METHOD_POST:
                $this->request = &$_POST;
                break;
        }
        return [
            self::REQUEST_P_METHOD => $this->method,
            self::REQUEST_P_REQUEST => $this->request,
            self::REQUEST_P_COOKIE => $this->cookie
        ];
    }
    
    /**
     * assignServer
     * 
     */
    private function assignServer() {
        $this->server = $_SERVER;
        return $this;
    }
    
    /**
     * assignMethod
     * 
     * @return $this
     */
    private function assignMethod() {
        $this->method = $this->getServer(self::REQUEST_METHOD);
        return $this;
    }
    
    /**
     * assignCookie
     * 
     * @return $this
     */
    private function assignCookie() {
        $this->cookie = $_COOKIE;
        return $this;
    }

}
