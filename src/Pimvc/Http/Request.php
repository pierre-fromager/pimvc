<?php

/**
 * Description of Pimvc\Http\Request
 *
 * @author Pierre Fromager
 */

namespace Pimvc\Http;

class Request implements Interfaces\Request{

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
     * getScheme
     * 
     * @return string
     */
    public function getScheme() {
        return $this->getServer(self::REQUEST_SCHEME);
    }
    
    /**
     * getHost
     * 
     * @return string
     */
    public function getHost() {
        return $this->getServer(self::REQUEST_HOST);
    }
    
    /**
     * getUrl
     * 
     * @return string
     */
    public function getUrl() {
        return  $this->getBaseUrl() . $this->getUri();
    }
    
    /**
     * getBaseUrl
     * 
     * @return string
     */
    public function getBaseUrl() {
        return  $this->getScheme() . self::SCHEME_SUFFIX . $this->getHost();
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
        $fragments = [];
        parse_str(parse_url($query)[self::REQUEST_QUERY], $fragments);
        return $fragments;
    }
    
    /**
     * getQueryTupple
     * 
     * @param string $query
     * @return array
     */
    public function getQueryTupple($query = '') {
        $array = explode(self::REQUEST_SLASH, ($query) ? $query : $this->getUri());
        array_shift($array);
        $keys = $values = [];
        $aSize = count($array);
        if ($aSize & 1) {
            array_push($array, null);
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
     * getParams
     * 
     * @return array
     */
    public function getParams() {
        return $this->get()[self::REQUEST_P_REQUEST];
    }

    /**
     * startSession
     * 
     */
    public function startSession() {
        session_name(sha1($this->getBaseUrl()));
        session_start();
        return $this;
    }
    
    /**
     * set
     * 
     * @param string $name
     * @param mixed $value 
     * @param string $key 
     */
    public function setSession($name, $value, $key = '') {
        if ($key) {
            $_SESSION[$name][$key] = $value;
        } else {
            $_SESSION[$name] = $value;
        }
        return $this;
    }
    
    /**
     * deleteSession
     * 
     * @param string $name
     * @param string $key 
     */
    public function deleteSession($name, $key = '') {
        if ($key) {
            unset($_SESSION[$name][$key]);
        } else {
            unset($_SESSION[$name]);
        }
        return $this;
    }

    /**
     * hasSession
     * 
     * @param string $name
     * @param string $key
     * @return boolean 
     */
    public function hasSession($name, $key = '') {
        if (!$key) {
            return (isset($_SESSION[$name]) && !empty($_SESSION[$name]));
        }
        return (isset($_SESSION[$name][$key]) && !empty($_SESSION[$name][$key]));
    }

    /**
     * getSession
     * 
     * @param string $name
     * @param string $key
     * @return mixed 
     */
    public function getSession($name, $key = '') {
        if (!$key) {
            return ($this->hasSession($name)) ? $_SESSION[$name] : '';
        }
        return ($this->hasSession($name, $key)) ? $_SESSION[$name][$key] : '';
    }
    
    /**
     * getExtRemoteAddr
     * 
     * @return string 
     */
    public function getRemoteAddr() {
        $headers = array();
        if (function_exists(self::PARAM_APACHE_HEADERS)) {
            $headers = apache_request_headers();
        }
        if ($headers) {
            if (array_key_exists(self::PARAM_X_FORWARD, $headers)
                && $this->isValidIpv4($headers[self::PARAM_X_FORWARD])
            ) {
                return $headers[self::PARAM_X_FORWARD];
            } elseif (array_key_exists(self::PARAM_HTTP_X_FORWARD, $headers)
                && $this->isValidIpv4($headers[self::PARAM_HTTP_X_FORWARD])
            ) {
                return $headers[self::PARAM_HTTP_X_FORWARD];
            }
        }
        return $this->getServer(self::PARAM_SERVER_REMOTE_ADDR);
    }

    /**
     * assignServer
     * 
     */
    private function assignServer() {
        $this->server = &$_SERVER;
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
