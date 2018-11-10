<?php
/**
 * Description of Pimvc\App
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */

namespace Pimvc;

use Pimvc\Http\Routes;
use Pimvc\Http\Router;
use Pimvc\Http\Request;
use Pimvc\Http\Response;
use Pimvc\Http\Middleware;
use Pimvc\View;
use Pimvc\Controller;
use Pimvc\Config as appConfig;
use Pimvc\Storage;
use Pimvc\Tools\Translate\Adapter\Csv as translatorAdpater;
use Pimvc\Tools\Translator as translate;

class App implements Interfaces\App
{

    public $hash;
    private static $instance = null;
    public $logger = null;
    protected $storage = null;
    protected $config = null;
    protected $routes = null;
    protected $router = null;
    protected $controller = null;
    public $path = null;
    protected $request = null;
    public $response = null;
    public $view = null;
    public $db = null;
    public $locale = null;
    public $translator = null;
    public $middlewareItems = [];
    public $middleware;

    /**
     * __construct
     *
     * @param appConfig $config
     * @return $this
     */
    public function __construct(appConfig $config)
    {
        $this->setConfig($config);
        $this->request = new Request($config);
        $this->setRoutes()->setRouter()->setResponse()->setView()->setController();
        $this->hash = spl_object_hash($this);
        $this->storage = new Storage();
        $this->setLocale($this->getConfig()->getSettings(self::APP_APP)[self::APP_DEFAULT_LOCALE]);
        self::$instance = $this;
        return self::$instance;
    }

    /**
     * setConfig
     *
     * @param config $config
     * @throws \Exception
     */
    private function setConfig(Config $config)
    {
        $this->config = $config;
    }

    /**
     * setRoutes
     *
     * @return $this
     */
    public function setRoutes()
    {
        $this->routes = new Routes($this->getConfig()->getSettings(self::APP_ROUTES));
        return $this;
    }

    /**
     * setRouter
     *
     * @return $this
     */
    public function setRouter()
    {
        $this->router = new Router($this->routes, $this->request);
        return $this;
    }

    /**
     * setResponse
     *
     * @return $this
     */
    public function setResponse()
    {
        $this->response = new Response();
        return $this;
    }

    /**
     * setView
     *
     * @return $this
     */
    public function setView()
    {
        $this->view = new View();
        return $this;
    }

    /**
     * setController
     *
     * @return $this
     */
    public function setController()
    {
        $classPrefix = $this->getConfig()->getSettings(self::APP_CLASSES)[self::APP_PREFIX];
        $this->controller = new Controller($this);
        $this->controller->setClassPrefix($classPrefix);
        return $this;
    }

    /**
     * setMiddleware
     *
     * @return $this
     */
    public function setMiddleware()
    {
        $middlwaresClasses = $this->getConfig()->getSettings(self::APP_MIDDLEWARE);
        foreach ($middlwaresClasses as $name => $middleware) {
            $this->middlewareItems[$name] = new $middleware;
        }
        $this->middleware = new Middleware();
        $this->middleware->layer($this->middlewareItems)->peel(
            $this->controller,
            function ($object) {
                return $object;
            }
        );
        return $this;
    }

    /**
     * setTranslator
     *
     * @return $this
     */
    public function setTranslator()
    {
        $requestLang = $this->request->getLang();
        $this->translator = new translate(new translatorAdpater($requestLang));
        return $this;
    }

    /**
     * getTranslator
     *
     * @return Tools\Translator
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     * setLocale
     *
     * @param string $locale
     * @return $this
     */
    public function setLocale($locale)
    {
        $this->locale = ($locale) ? $locale : locale_get_default();
        ini_set('intl.default_locale', $locale);
        return $this;
    }

    /**
     * getLogger
     *
     * @return string
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * setLogger
     *
     * @return string
     */
    public function setLogger()
    {
        $this->logger = Logger::getFileInstance(
            $this->getPath() . Logger::LOG_ADAPTER_FILE_PATH,
            Logger::DEBUG,
            Logger::LOG_ADAPTER_FILE
        );
        return $this;
    }

    /**
     * getLocale
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * getInstance
     *
     * @return \Pimvc\App
     */
    public static function getInstance()
    {
        return (self::$instance instanceof \Pimvc\App) ? self::$instance : false;
    }

    /**
     * setPath
     *
     * @param string $path
     * @return $this
     */
    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * getRouter
     *
     * @return router
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * getRequest
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * setRequest
     *
     * @return this
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * getRoutes
     *
     * @return routes
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * getResponse
     *
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * getView
     *
     * @return Pimvc\View
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * getPath
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * getController
     *
     * @return Pimvc\Controller
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * getConfig
     *
     * @return Pimvc\Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * getConfig
     *
     * @return Pimvc\Storage
     */
    public function getStorage()
    {
        return $this->storage;
    }

    /**
     * run
     *
     * @return type
     */
    public function run()
    {
        return $this->setMiddleware()
                ->getController()
                ->setDefault()
                ->run()
                ->dispatch();
    }
}
