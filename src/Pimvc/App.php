<?php

/**
 * Description of Pimvc\App
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */

namespace Pimvc;

class App implements Interfaces\App
{

    private static $instance = null;
    protected $storage = null;
    protected $config = null;
    protected $routes = null;
    protected $router = null;
    protected $controller = null;
    protected $request = null;
    public $hash;
    public $logger = null;
    public $path = null;
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
     * @param \Pimvc\Config $config
     * @return \Pimvc\App
     */
    public function __construct(\Pimvc\Config $config)
    {
        $this->setConfig($config);
        $this->request = new \Pimvc\Http\Request($config);
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
     * @param \Pimvc\Config $config
     */
    private function setConfig(\Pimvc\Config $config)
    {
        $this->config = $config;
    }

    /**
     * setRoutes
     *
     * @return \Pimvc\App
     */
    public function setRoutes(): \Pimvc\App
    {
        $routesConfig = $this->getConfig()->getSettings(self::APP_ROUTES);
        $this->routes = new \Pimvc\Http\Routes($routesConfig);
        return $this;
    }

    /**
     * setRouter
     *
     * @return \Pimvc\App
     */
    public function setRouter(): \Pimvc\App
    {
        $this->router = new \Pimvc\Http\Router($this->routes, $this->request);
        return $this;
    }

    /**
     * setResponse
     *
     * @return \Pimvc\App
     */
    public function setResponse(): \Pimvc\App
    {
        $this->response = new \Pimvc\Http\Response();
        return $this;
    }

    /**
     * setView
     *
     * @return \Pimvc\App
     */
    public function setView(): \Pimvc\App
    {
        $this->view = new \Pimvc\View();
        return $this;
    }

    /**
     * setController
     *
     * @return \Pimvc\App
     */
    public function setController(): \Pimvc\App
    {
        $classPrefix = $this->getConfig()->getSettings(self::APP_CLASSES)[self::APP_PREFIX];
        $this->controller = new \Pimvc\Controller($this);
        $this->controller->setClassPrefix($classPrefix);
        return $this;
    }

    /**
     * setMiddleware
     *
     * @return $this
     */
    public function setMiddleware(): \Pimvc\App
    {
        $middlwaresClasses = $this->getConfig()->getSettings(self::APP_MIDDLEWARE);
        foreach ($middlwaresClasses as $name => $middleware) {
            $this->middlewareItems[$name] = new $middleware;
        }
        $this->middleware = new \Pimvc\Http\Middleware();
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
     * @return \Pimvc\App
     */
    public function setTranslator(): \Pimvc\App
    {
        $requestLang = $this->request->getLang();
        $this->translator = new \Pimvc\Tools\Translator(
            new \Pimvc\Tools\Translate\Adapter\Csv($requestLang)
        );
        return $this;
    }

    /**
     * getTranslator
     *
     * @return \Pimvc\Tools\Translator
     */
    public function getTranslator(): \Pimvc\Tools\Translator
    {
        return $this->translator;
    }

    /**
     * setLocale
     *
     * @param string $locale
     * @return \Pimvc\App
     */
    public function setLocale(string $locale): \Pimvc\App
    {
        $this->locale = ($locale) ? $locale : locale_get_default();
        ini_set('intl.default_locale', $locale);
        return $this;
    }

    /**
     * getLogger
     *
     * @return \Pimvc\Logger
     */
    public function getLogger(): \Pimvc\Logger
    {
        return $this->logger;
    }

    /**
     * setLogger
     *
     * @return \Pimvc\App
     */
    public function setLogger(): \Pimvc\App
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
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * getInstance
     *
     * @return \Pimvc\App
     * @throws \Exception
     */
    public static function getInstance()
    {
        $isAppInstance = self::$instance instanceof \Pimvc\App;
        if (!$isAppInstance) {
            throw new \Exception('App is not a Pimvc\App instance');
        }
        return self::$instance;
    }

    /**
     * setPath
     *
     * @param string $path
     * @return \Pimvc\App
     */
    public function setPath(string $path): \Pimvc\App
    {
        $this->path = $path;
        return $this;
    }

    /**
     * getRouter
     *
     * @return \Pimvc\Http\Router
     */
    public function getRouter(): \Pimvc\Http\Router
    {
        if (!($this->router instanceof \Pimvc\Http\Router)) {
            throw new \Exception(
                'Not a \Pimvc\Http\Router instance'
            );
        }
        return $this->router;
    }

    /**
     * getRequest
     *
     * @return \Pimvc\Http\Request
     */
    public function getRequest(): \Pimvc\Http\Request
    {
        return $this->request;
    }

    /**
     * setRequest
     *
     * @return \Pimvc\App
     */
    public function setRequest(\Pimvc\Http\Request $request): \Pimvc\App
    {
        $this->request = $request;
        return $this;
    }

    /**
     * getRoutes
     *
     * @return \Pimvc\Http\Routes
     */
    public function getRoutes(): \Pimvc\Http\Routes
    {
        return $this->routes;
    }

    /**
     * getResponse
     *
     * @return \Pimvc\Http\Response
     */
    public function getResponse(): \Pimvc\Http\Response
    {
        return $this->response;
    }

    /**
     * getView
     *
     * @return \Pimvc\View
     */
    public function getView(): \Pimvc\View
    {
        return $this->view;
    }

    /**
     * getPath
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * getController
     *
     * @return \Pimvc\Controller
     */
    public function getController(): \Pimvc\Controller
    {
        return $this->controller;
    }

    /**
     * getConfig
     *
     * @return Pimvc\Config
     */
    public function getConfig(): \Pimvc\Config
    {
        return $this->config;
    }

    /**
     * getConfig
     *
     * @return Pimvc\Storage
     */
    public function getStorage(): \Pimvc\Storage
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
