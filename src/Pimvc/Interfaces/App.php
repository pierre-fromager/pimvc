<?php

/**
 * Description of interface AppInterface
 *
 * @author pierrefromager
 */

namespace Pimvc\Interfaces;


interface App {

    public function __construct(\Pimvc\Config $config);
    
    public function setPath($path);

    public function getRouter();

    public function getRequest();

    public function getRoutes();

    public function getResponse();

    public function getView();

    public function getPath();

    public function getController();
    
    public function getConfig();

    public function run();
}