<?php

/**
 * Description of Pimvc\Http\Request
 *
 * @author Pierre Fromager
 */

namespace Pimvc\Http;

class Request implements Interfaces\Request, \SplSubject{
    
    private $request;
    private $url;
    private $uri;
    private $method;
    private $cookie;
    private $server;
    private $isSapi;
    private $options;

    /**
     * __construct
     * 
     * @return $this
     */
    public function __construct(\Pimvc\Config $config) {
        $this->setSapi()
            ->setOptions($config)
            ->setServer()
            ->setUri()
            ->setMethod()
            ->setCookie()
            ->startSession();
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
        return $this->uri;
    }
    
    /**
     * setUri
     * 
     * @param string $uri
     * @return Request
     */
    public function setUri($uri = '') {
        $this->uri = ($uri) ? $uri : $this->getServer(self::REQUEST_URI);
        return $this;
    }
    
    /**
     * getScheme
     * 
     * @return string
     */
    public function getScheme() {
        return ($this->options->scheme) 
            ? $this->options->scheme 
            : $this->getServer(self::REQUEST_SCHEME);
    }
    
    /**
     * getHost
     * 
     * @return string
     */
    public function getHost() {
        return ($this->options->hostname) 
            ? $this->options->hostname 
            : $this->getServer(self::REQUEST_HOST);
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
        if (!$param) {
            return $this->server;
        }
        return (isset($this->server[$param])) 
            ? $this->server[$param] 
            : '';
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
            case self::REQUEST_METHOD_PUT:
            case self::REQUEST_METHOD_PATCH:
            case self::REQUEST_METHOD_DELETE:
            case self::REQUEST_METHOD_COPY:
            case self::REQUEST_METHOD_HEAD:
            case self::REQUEST_METHOD_OPTIONS:
            case self::REQUEST_METHOD_LINK:
            case self::REQUEST_METHOD_UNLINK:
            case self::REQUEST_METHOD_PURGE:
            case self::REQUEST_METHOD_LOCK:
            case self::REQUEST_METHOD_UNLOCK:
            case self::REQUEST_METHOD_PROPFIND:
            case self::REQUEST_METHOD_VIEW:
                $this->request = $this->getInput();
                break;
        }
        return [
            self::REQUEST_P_METHOD => $this->method,
            self::REQUEST_P_REQUEST => $this->request,
            self::REQUEST_P_COOKIE => $this->cookie
        ];
    }
    
    /**
     * getInput
     * 
     * @return array
     */
    private function getInput() {
        $input = [];
        $inputContent = file_get_contents(self::REQUEST_INPUT);
        if ($this->isJson($inputContent)) {
            $input = json_decode($inputContent, true);
        } else {
            parse_str($inputContent, $input);
        }
        return $input;
    }

    /**
     * isJson
     * 
     * @param type $string
     * @return boolean
     */
    private function isJson($string) {
        $isContentTypeJson = $this->contentType() === 'application/json';
        if (!$isContentTypeJson) {
            return false;
        }
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
    
    /**
     * contentType
     * 
     * @return string
     */
    public function contentType(){
        return $this->getServer(self::REQUEST_CONTENT_TYPE);
    }

    /**
     * getParams
     * 
     * @return array
     */
    public function getParams($key = '') {
        $params = $this->get()[self::REQUEST_P_REQUEST];
        return ($key && isset($params[$key])) ? $params[$key] : $params;
    }

    /**
     * startSession
     * 
     */
    public function startSession() {
        if (!$this->isSapi) {
            session_name(sha1($this->getBaseUrl()));
            session_start();
        }
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
                //&& $this->isValidIpv4($headers[self::PARAM_X_FORWARD])
            ) {
                return $headers[self::PARAM_X_FORWARD];
            } elseif (array_key_exists(self::PARAM_HTTP_X_FORWARD, $headers)
               // && $this->isValidIpv4($headers[self::PARAM_HTTP_X_FORWARD])
            ) {
                return $headers[self::PARAM_HTTP_X_FORWARD];
            }
        }
        return $this->getServer(self::PARAM_SERVER_REMOTE_ADDR);
    }
    
    /**
     * getCookie
     * 
     * @param string $name
     * @return string
     */
    public function getCookie($name) {
        return (isset($this->cookie[$name])) ? $this->cookie[$name] : '';
    }

    /**
     * setServer
     * 
     */
    public function setServer($server = []) {
        $this->server = ($this->isSapi) ? $server : $_SERVER;
        return $this;
    }
    
    /**
     * assignMethod
     * 
     * @param type $method
     * @return $this
     */
    public function setMethod($method = 'GET') {
        $this->method = ($this->isSapi) 
            ? $method 
            : $this->getServer(self::REQUEST_METHOD);
        return $this;
    }
    
    /**
     * isSapi
     * 
     * @return $this
     */
    private function setSapi() {
        $this->isSapi = (php_sapi_name() === self::REQUEST_SAPI_CLI);
        return $this;
    }
    
    /**
     * setOptions
     * 
     * @return $this
     */
    public function setOptions(\Pimvc\Config $config) {
        $this->options =  (new \Pimvc\Http\Request\Options())->load($config);
        return $this;
    }

    /**
     * assignCookie
     * 
     * @return $this
     */
    private function setCookie() {
        $this->cookie = &$_COOKIE;
        return $this;
    }

    public function attach(\SplObserver $observer) {
        
    }

    public function detach(\SplObserver $observer) {
        
    }

    public function notify() {
        
    }

}
