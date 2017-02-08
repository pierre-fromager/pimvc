<?php

namespace lib;

/**
 * Description of request
 *
 * @author Pierre Fromager
 */
class request {

    const REQUEST_METHOD = 'REQUEST_METHOD';
    const REQUEST_METHOD_GET = 'GET';
    const REQUEST_METHOD_POST = 'POST';
    const REQUEST_P_METHOD = 'method';
    const REQUEST_P_REQUEST = 'request';
    const REQUEST_P_COOKIE = 'cookie';

    private $request;
    private $method;
    private $cookie;

    /**
     * __construct
     * 
     * @return $this
     */
    public function __construct() {
        $this->assignRequest()->assignMethod()->assignCookie();
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
     * assignRequest
     * 
     */
    private function assignRequest() {
        //$this->request = $this->get();
        $this->request = $_REQUEST;
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


    /**
     * getServer
     * 
     * @param string $param
     * @return array
     */
    private function getServer($param = '') {
        return ($param) ? $_SERVER[$param] : $_SERVER;
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

}
