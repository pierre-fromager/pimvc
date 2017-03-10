<?php

/**
 * Description of routes
 *
 * @author pierrefromager
 */


namespace pimvc\http;

class routes implements interfaces\routes{
    
    private $routes = [];
    
    /**
     * __construct
     * 
     * @param array $routes
     * @return $this
     */
    public function __construct($routes) {
        $this->setRoutes($routes);
        return $this;
    }

    /**
     * getRoutes
     * 
     * @return array
     */
    public function getRoutes() {
        return $this->routes;
    }
    
    /**
     * setRoutes
     * 
     * @param array $routes
     * @return $this
     */
    public function setRoutes($routes) {
        if (!$routes || !is_array($routes)) {
            throw new \Exception(self::BAD_ROUTE_COLLECTION);
        }
        $this->routes = $routes;
        return $this;
    }

}
