<?php

namespace lib;

/**
 * routes
 * 
 */
class routes {
    
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
        $this->routes = $routes;
        return $this;
    }

}
