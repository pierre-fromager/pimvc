<?php

/**
 * Pimvc\Tools
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */

namespace Pimvc\Tools;

use \Pimvc\File\System\Scanner as fileScanner;
use Pimvc\Tools\Session as toolsSession;
use \Pimvc\Tools\Format\Roles as roleFormater;
use Pimvc\Cache\Factory as cacheFactory;

class Acl {
    
    const ACL_CONTROLLER_EXT = '.php';
    const ACL_ALLOW = 'allow';
    const ACL_DENY = 'deny';
    const ACL_DEFAULT = self::ACL_DENY;
    const ACL_CONTROLLER_SUFFIX_LEN = -14;
    const ACL_CONTROLLER_SUFFIX = 'Controller';
    const ACL_ACTION_SUFFIX = 'Action';
    const ACL_DIRECTORY = '/config/';
    const ACL_FILE = 'acl.json';
    const ACL_FILE_ERROR = 'Erreur sur le fichier d\'acl.';
    const ACL_READ_ERROR = 'Erreur de lecture d\'acl.';
    const ACL_ROLE_ANONYMOUS = 'anonymous';
    const ACL_ROLE_WAITING = 'waiting';
    const ACL_ROLE_USER = 'user';
    const ACL_ROLE_PRO = 'pro';
    const ACL_ROLE_ADMIN = 'admin';
    const ACL_CACHE_KEY = 'acl_ressources';
 
    const ACL_ACTION_FILTER_CALLBACK = 'actionCheck';
    const ACL_ACTION_MAP_CALLBACK = 'actionShort';
    const ACL_CACHE_ADAPTER = 'file';
    const ACL_CACHE_EXPIRATION = 86400;
    
    const PARAM_UNDERSCORE = '_';
    const PARAM_EMPTY = '';

    protected $controllerPath = '';
    protected $controllerFileList = array();
    protected $ressourceList = array();
    protected $controllerActionList = array();
    protected $roles = array();
    protected $aclFilename = '';
    protected $isPhp = false;
    
    protected $errors = [];
    private $app;

    /**
     * @see __construct
     */
    public function __construct($reset = false, $xmlMode = false) {
        $this->app = \Pimvc\App::getInstance();
        $appPath = $this->app->getPath();
        $this->controllerPath = $appPath . self::ACL_CONTROLLER_SUFFIX;
        $this->controllerFileList = $this->getControllerFileList();
        $this->aclFilename = $appPath . self::ACL_DIRECTORY . self::ACL_FILE;
        $this->isPhp = $xmlMode;
        if ($reset) {
            $this->initRessources();
            $this->save();
        } else {
            $this->load();
        }
    }
    
    /**
     * getErrors
     * 
     * @return array
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
     * getControllerFileList
     * 
     * @return array 
     */
    protected function getControllerFileList() {
        $scanner = new fileScanner(
            $this->controllerPath . DIRECTORY_SEPARATOR
            , []
            , []
            , $dirInclude = true
            , $showDir = false
        );
        $scanner->process();
        $controlerList = $scanner->filesScanned;
        unset($scanner);
        return $controlerList;
    }

    /**
     * getControllerList
     * 
     * @return array 
     */
    protected function getControllerList() {
        $ctrlFilelist = $this->getControllerFileList();
        return array_map(array($this, 'getControlerClassname'), $ctrlFilelist);
    }

    /**
     * getControlerClassname
     * 
     * @param string $controllerFilename
     * @return string 
     */
    protected function getControlerClassname($controllerFilename) {
        $baseClass = $this->getNamespaceCtrlPrefix() . '\\' . basename($controllerFilename);
         $className = substr($baseClass, 0, -4);
        return $className;
    }
    
    /**
     * getNamespaceCtrlPrefix
     * 
     * @return string
     */
    public function getNamespaceCtrlPrefix(){
        return str_replace('\App', '\Controller', get_class($this->app));
    }

    /**
     * actionCheck check if method is an action
     * 
     * @param string $methodName
     * @return boolean 
     */
    protected static function actionCheck($methodName) {
        return (substr($methodName, -6) == self::ACL_ACTION_SUFFIX);
    }

    /**
     * actionShort returns action short name.
     * 
     * @param string $methodName
     * @return string 
     */
    protected static function actionShort($methodName) {
        return substr($methodName, 0, -6);
    }

    /**
     * getPublicMethods
     * 
     * @param string $classname
     * @return array 
     */
    protected function getPublicMethods($classname) {
        $classReflex = new \ReflectionClass($classname);
        $methodList = $classReflex->getMethods(
           \ReflectionMethod::IS_FINAL | \ReflectionMethod::IS_PUBLIC
        );
        unset($classReflex);
        $classnameFilter = substr($classname, 1, strlen($classname));
        $filteredList = [];
        foreach ($methodList as $method) {
            if ($method->class === $classnameFilter) {
                $filteredList[] = $method;
            }
        }
        unset($methodList);
        return $filteredList;
    }

    /**
     * getRoles
     * 
     * @return array 
     */
    protected function getRoles() {
        return roleFormater::getList();
    }
    
    /**
     * isNew return true if action or controller or role is new.
     * 
     * @param string $controller
     * @param string $action
     * @param string $role
     * @return boolean 
     */
    private function isNew($controller, $action, $role) {       
        $isNew = ($this->get($controller, $action, $role) == self::ACL_READ_ERROR);
        return $isNew;
    }
    
    /**
     * getDefaultAcl returns the default acl for a given role
     * 
     * @param string $role
     * @return string 
     */
    private function getDefaultAcl($role) {
        return ($role == self::ACL_ROLE_ADMIN) 
            ? self::ACL_ALLOW 
            : self::ACL_DEFAULT;
    }
    
    /**
     * getScanRessources scans ressources.
     * 
     * @param boolean $reset
     * @return array 
     */
    private function getScanRessources($reset = false) {
        $ressource = array();
        /*
        $cacheRessources = cacheFactory::get(
            self::ACL_CACHE_ADAPTER
            , self::ACL_CACHE_KEY
            , self::ACL_CACHE_EXPIRATION
        );
        $useCache = false;*/
        //if ($cacheRessources->expired() || toolsSession::isAdmin() || !$useCache
        //        ) {
            $roleList = array_flip($this->getRoles());
            $cttlList = $this->getControllerList();
            foreach ($cttlList as $controller) {
                $controllerNs = '\\' . $controller;
                $actions = [];
                $actionsList = $this->getActionReflex($controllerNs);
                foreach ($actionsList as $action => $value) {
                    if (!in_array($action, ['__construct', '__destruct', '__toString'])) {
                        foreach ($roleList as $role => $acl) {
                            $isNew = $this->isNew($controller, $action, $role);
                            $acl = ($isNew || $reset)
                              ? $this->getDefaultAcl($role)
                              : $this->get($controller, $action, $role);
                            $roleList[$role] = $acl;
                            $this->set($controller, $action, $role, $acl, $isNew);
                        }
                        $actions[$action] = $roleList;
                    }
                }
                $ressource[$controller] = $actions;
            }
           //$cacheRessources->set(self::ACL_CACHE_KEY, $ressource);
        /*} else {
            $ressource = $cacheRessources->get(self::ACL_CACHE_KEY);
        }*/
        //unset($cacheRessources);
        return $ressource;
    }
    
    /**
     * getActionReflex
     * 
     * @param type $controllerNs
     * @return type
     */
    private function getActionReflex($controllerNs) {
        $actions = $this->getPublicMethods($controllerNs);
        $actionList = [];
        foreach ($actions as $action) {
            $actionList[] = $action->name;
        }
        return array_flip($actionList);
    }

    /**
     * initRessources init default acl policy.
     * 
     */
    protected function initRessources() {
        $this->ressourceList = $this->getScanRessources($reset = true);
    }
    
    /**
     * getRessources returns ressources
     * 
     * @return array 
     */
    public function getRessources() {
        $this->load();
        return $this->ressourceList;
    }

    /**
     * load
     * 
     * @return string 
     */
    protected function load() {
        $result = self::ACL_FILE_ERROR;
        if (file_exists($this->aclFilename)) {
            $this->ressourceList = json_decode(
                file_get_contents($this->aclFilename), 
                true
            );
            $this->ressourceList = $this->getScanRessources($reset = false);
        } else {
            $this->errors[] = 'Missing config file.';
            throw new \Exception('Missing config file in :' . $this->aclFilename);
        }
        return $result;
    }

    /**
     * save saves acl.
     * 
     * @return string 
     */
    protected function save() {
        $result = self::ACL_FILE_ERROR;
        if (file_exists($this->aclFilename)) {
            $aclContent = json_encode($this->ressourceList, JSON_PRETTY_PRINT);
            $aclContent = str_replace($this->getNamespaceCtrlPrefix(), '', $aclContent);
            file_put_contents($this->aclFilename, $aclContent);
            //$cacheRessources = new \Pimvc\Cache('acl_ressources', 20);
            //$cacheRessources = cacheFactory::get(\Pimvc\Cache::DEFAULT_ADPATER,'acl_ressources', 20);
            //$cacheRessources->set('acl_ressources',$aclContent);
            /*
            $cacheRessources = Lib_Cache_Factory::get(
                self::ACL_CACHE_ADAPTER
                , 'acl_ressources'
                , 20
            );
            $cacheRessources->set('acl_ressources',$aclContent);*/
            //unset($cacheRessources);
        } else {
            throw new \Exception('Misssing reference file ' . $this->aclFilename);
        }
        return $result;
    }

    /**
     * get returns acl for given controller, action and role
     * 
     * @param string $controller
     * @param string $action
     * @param string $role
     * @return string 
     */
    public function get($controller, $action, $role) {
        return (isset($this->ressourceList[$controller][$action][$role])) 
            ? (string) $this->ressourceList[$controller][$action][$role] 
            : self::ACL_READ_ERROR;
    }

    /**
     * set and save acl.
     * 
     * @param string $controller
     * @param string $action
     * @param string $role
     * @param string $acl 
     */
    public function set($controller, $action, $role, $acl, $save = true) {
        $this->ressourceList[$controller][$action][$role] = $acl;
        if ($save) $this->save();
    }
    
    /**
     * isAllowed return true if acl is allowed.
     * 
     * @param string $controller
     * @param string $action
     * @param string $role
     * @return boolean 
     */
    public function isAllowed($controller, $action, $role) {
        return ($this->get($controller, $action, $role) == self::ACL_ALLOW);
    }

}