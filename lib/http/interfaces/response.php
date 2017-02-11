<?php

namespace lib\http\interfaces;

interface responseInterface {

    public function __construct($content = []);
            
    public function setContent($content = []);

    public function setHttpCode($code = 200);

    public function setType($type = null);

    public function withCookie($name = '', $value = '', $ttl = 3600, $path = '/', $domain = '', $secure = false, $httponly = true);

    public function removeHeaders();

    public function redirect($url);

    public function sendHeaders();

    public function dispatch();
}
