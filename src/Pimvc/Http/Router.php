<?php

/**
 * Description of router
 *
 * @author pierrefromager
 */

namespace Pimvc\Http;

class Router implements Interfaces\Router
{
    private $uri = null;
    private $server = null;
    private $routes = null;
    private $request = null;
    
    /**
     *
     * @param \Pimvc\Http\Routes $routes
     * @param \Pimvc\Http\Request $request
     * @return \Pimvc\Http\Router
     */
    public function __construct(\Pimvc\Http\Routes $routes, \Pimvc\Http\Request $request)
    {
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
    public function getUri(): string
    {
        return $this->uri;
    }
    
    /**
     * setUri
     *
     * @return string
     */
    public function setUri($uri = ''): \Pimvc\Http\Router
    {
        $this->uri = ($uri) ? $uri : substr($this->request->getUri(), 1);
        return $this;
    }

    /**
     * getFragments
     *
     * @return array
     */
    public function getFragments(): array
    {
        return explode(self::URI_SEPARATOR, $this->getUri());
    }
    
    /**
     * compile
     *
     * @return array
     */
    public function compile(): array
    {
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
        return [];
    }
}
