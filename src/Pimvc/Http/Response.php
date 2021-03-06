<?php

/**
 * Description of response
 *
 * @author pierrefromager
 */

namespace Pimvc\Http;

class Response implements Interfaces\Response
{
    private $content;
    private $type;
    private $headers;
    private $httpCode;
    private $httpCodes = self::HTTP_CODES;
    private $redirectUrl = '';

    /**
     * __construct
     *
     * @param array $content
     * @param string $type
     * @return $this
     */
    public function __construct()
    {
        return $this;
    }

    /**
     * setHeaders
     *
     * @param array $headers
     * @return $this
     */
    public function setHeaders(array $headers = [])
    {
        if ($headers) {
            $this->headers = $headers;
        } else {
            $this->setDefaultHeaders();
        }
        return $this;
    }

    /**
     * setContent
     *
     * @param array $content
     * @return $this
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }
    
    /**
     * setHttpCode
     *
     * @param int $code
     * @return $this
     */
    public function setHttpCode($code = 200)
    {
        $this->httpCode = $code;
        return $this;
    }

    /**
     * setType
     *
     * @param string $type
     * @return $this
     */
    public function setType($type = null)
    {
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
    public function withCookie($name = '', $value = '', $ttl = 3600, $path = '/', $domain = '', $secure = false, $httponly = true)
    {
        if ($name) {
            setcookie($name, $value, time() + $ttl, $path, $domain, $secure, $httponly);
            $_COOKIE[$name] = $value;
        }
        return $this;
    }
    
    /**
     * removeHeaders
     *
     * @return $this
     */
    public function removeHeaders()
    {
        header_remove();
        return $this;
    }
    
    /**
     * redirect
     *
     * @param type $url
     */
    public function redirect($url)
    {
        $this->redirectUrl = $url;
        return $this;
    }

    /**
     * sendHeaders
     *
     */
    public function sendHeaders()
    {
        $headersLength = count($this->headers);
        for ($i = 0; $i < $headersLength; $i++) {
            header($this->headers[$i]);
        }
    }
    
    /**
     * isJsonType
     *
     * @return boolean
     */
    public function isJsonType()
    {
        return ($this->type === self::TYPE_JSON);
    }

    /**
     * dispatch
     *
     */
    public function dispatch($andDie = false)
    {
        http_response_code($this->httpCode);
        if ($this->redirectUrl) {
            header(self::HEADER_LOCATION . $this->redirectUrl);
            die;
        } else {
            $this->setHeaders()->sendHeaders();
            echo ($this->isJsonType())
                ? json_encode($this->content, JSON_PRETTY_PRINT)
                : (string) $this->content;
        }
        if ($andDie) {
            die;
        }
    }

    /**
     * setDefaultHeaders
     *
     */
    private function setDefaultHeaders()
    {
        $this->headers[] = self::HTTP_1 . $this->httpCodes[$this->httpCode];
        $this->headers[] = self::HEADER_CACHE_CONTROL;
        $this->headers[] = self::HEADER_CACHE_EXPIRE;
        $this->headers[] = $this->getContentType($this->type);
    }

    /**
     * getContentType
     *
     * @param string $type
     * @return string
     */
    private function getContentType($type)
    {
        return self::CONTENT_TYPE . $type;
    }
}
