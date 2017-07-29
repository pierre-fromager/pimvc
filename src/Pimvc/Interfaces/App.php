<?php

/**
 * Description of interface AppInterface
 *
 * @author pierrefromager
 */

namespace Pimvc\Interfaces;

interface App {

    const APP_APP = 'app';
    const APP_REQUEST = 'request';
    const APP_PATH = 'APP_PATH';
    const APP_DEFAULT_LOCALE = 'defaultLocale';
    const APP_SSL = 'ssl';
    const APP_ROUTES = 'routes';
    const APP_CLASSES = 'classes';
    const APP_PREFIX = 'prefix';
    const APP_MIDDLEWARE = 'middleware';

    public function __construct(\Pimvc\Config $config);

    public function setTranslator();

    public function getTranslator();
    
    public function getLogger();
    
    public function setLogger();
    
    public function setMiddleware();

    public function setLocale($locale);

    public function getLocale();

    public function setPath($path);
    
    public function getPath();

    public function getRouter();

    public function getRequest();

    public function getRoutes();

    public function getResponse();

    public function getView();

    public function getController();

    public function getConfig();

    public function run();
}
