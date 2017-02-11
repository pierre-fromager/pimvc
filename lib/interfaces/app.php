<?php

/**
 * Description of interface appInterface
 *
 * @author pierrefromager
 */

namespace lib\interfaces;


interface appInterface {

    public function __construct(\lib\config $config);
    
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