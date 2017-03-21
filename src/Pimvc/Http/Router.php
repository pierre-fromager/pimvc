<?php

/**
 * Description of router
 *
 * @author pierrefromager
 */

namespace Pimvc\Http;

class Router implements Interfaces\Router{

    private $uri = null;
    private $server = null;
    private $routes = null;

    /**
     * __construct
     * 
     */
    public function __construct(routes $routes) {
        $this->server = $this->getServer();
        $this->uri = $this->server[self::REQUEST_URI];
        $this->uri = substr($this->uri, 1);
        $this->routes = $routes;
    }
    
    /**
     * getServer
     * 
     * @param string $param
     * @return string || array
     */
    private function getServer($param = null) {
        return ($param) ? $_SERVER[$param] : $_SERVER;
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
     * getFragments
     * 
     * @return array
     */
    public function getFragments() {
        return explode(self::URI_SEPARATOR, $this->getUri());
    }
    
    /**
     * compile
     * 
     * @return array
     */
    public function compile() {
        $routes = $this->routes->getRoutes();
        $routesLength = sizeof($routes);
        for ($i = 0; $i < $routesLength; $i++) {
            $match = preg_match($routes[$i], $this->getUri(), $matches);
            if ($match) {
                array_shift($matches);
                return $matches;
            }
        } 
    }
}
