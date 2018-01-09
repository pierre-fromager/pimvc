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
    private $request = null;

    /**
     * __construct
     * 
     */
    public function __construct(Routes $routes, Request $request) {
        $this->routes = $routes;
        $this->request = $request;
        $this->server = $this->request->getServer();
        $this->setUri();
        return $this;
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
     * @return string
     */
    public function setUri($uri = '') {
        $this->uri = ($uri) ? $uri : substr($this->request->getUri(), 1);
        return $this;
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
            $matches = [];
            $match = preg_match($routes[$i], $this->getUri(), $matches);
            if ($match) {
                array_shift($matches);
                return $matches;
            }
        } 
    }
}
