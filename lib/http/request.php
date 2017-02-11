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
    const REQUEST_QUERY = 'query';

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
        return ($this->getUri() === DIRECTORY_SEPARATOR);
    }
    
    /**
     * getUri
     * 
     * @return string
     */
    public function getUri() {
        return $this->getServer(self::REQUEST_URI);
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
     * getParsedQuery
     * 
     * @param type $query
     * @return type
     */
    public function getParsedQuery($query){
        parse_str(parse_url($query)[self::REQUEST_QUERY], $fragments);
        return $fragments;
    }
    
    /**
     * getParsedQueryTupple
     * 
     * @param type $query
     * @return type
     */
    public function getParsedQueryTupple($query){
        parse_str(parse_url($query)[self::REQUEST_QUERY], $fragments);
        return $fragments;
    }
    
    /**
     * getTupple
     * 
     * @param array $array
     * @param boolean $invert
     * @return array
     */
    private function getTupple($array = [], $invert = false) {
        return array_filter(
            $array ,
            function($k) use ($invert) {
                return ($invert) ? ($k % 1) == 0 : ($k % 2) == 0;
            }
        );
    }

    /**
     * getQueryTupple
     * 
     * @param string $query
     * @return array
     */
    public function getQueryTupple($query) {
        $qParams = explode('/', $query);
        $keys = $this->getTupple($qParams, true);
        $values = $this->getTupple($qParams, false);
        $tupple = array_combine($keys, $values);
        array_shift($tupple);
        return $tupple;
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
