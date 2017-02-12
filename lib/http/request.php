<?php

/**
 * Description of lib\http\request
 *
 * @author Pierre Fromager
 */

namespace lib\http;

class request implements interfaces\request{

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
    public function getServer($param) {
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
        parse_str(parse_url($query)[self::REQUEST_QUERY], $tupple);
        return $tupple;
    }
    
    /**
     * getQueryTupple
     * 
     * @param string $query
     * @return array
     */
    public function getQueryTupple($query) {
        $array = explode('/', $query);
        array_shift($array);
        $keys = $values = [];
        $aSize = count($array);
        if ($aSize & 1) {
            array_push ($array,null);
            $aSize = count($array);
        }
        for ($i = 0; $i < $aSize; $i++) {
            if ($i & 1)
                $values[] = $array[$i];
            else
                $keys[] = $array[$i];
        }
        return array_combine($keys, $values);
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
