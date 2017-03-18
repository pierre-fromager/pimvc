<?php

/**
 * Description of user controller
 *
 * @author Pierre Fromager
 */

namespace app1\controller;

use \pimvc\file\system\scanner;

class file extends \pimvc\controller\basic {

    const PARAM_ID = 'id';
    const PARAM_EMAIL = 'email';
    const PARAM_LOGIN = 'login';
    const PARAM_PASSWORD = 'password';
    const VIEW_USER_PATH = '/views/user/';
    const WILDCARD = '%';
    const PHP_EXT = '.php';
    const BACkSLASH = '\\';
    const SLASH = '/';
    const PARAM_NAMESPACE = 'namespace ';
    const _OLD = 'old';
    const _NEW = 'new';
    const _FILES = 'files';
    const _NAMESPACES = 'namespaces';
    const _CLASSES = 'classes';
    const _INSTANCES = 'instances';
    const _CONFIG = 'config';
    const _PHP = 'php';
    const PARAM_UPPER = 'ucfirst';

    private $scanner;
    private $rootDir;
    private $scannedFiles;
    private $transfo;

    /**
     * init
     * 
     */
    protected function init() {
        $this->rootDir = dirname($this->getApp()->getPath()) . self::SLASH;
    }

    /**
     * user
     * 
     * @return \pimvc\http\response
     */
    public function index() {
        $this->setScannedFiles()->setTransfo()->process();
        return $this->asJson($this->transfo);
    }
    
    /**
     * process
     */
    private function process() {
        foreach ($this->transfo as $key => $value) {
            $oldFile = $value[self::_FILES][self::_OLD];
            $newFile = str_replace(
                'src', 
                'src2', 
                $value[self::_FILES][self::_NEW]
            );
            $path = dirname($newFile);
            if (!is_dir($path)) {
                $mkres = mkdir($path, 0777, true);
                if ($mkres) {
                    $rescop = copy($oldFile, $newFile);
                }
            } else {
                $rescop = copy($oldFile, $newFile);
                file_put_contents(
                    $newFile, 
                    $this->processReplacer(file_get_contents($newFile), $value)
                );
            }
        }
    }

    /**
     * processReplacer
     * 
     * @param string $filecontent
     * @return string
     */
    private function processReplacer($filecontent, $value) {
        $filecontent = str_replace(
            $value[self::_NAMESPACES][self::_OLD], $value[self::_NAMESPACES][self::_NEW], $filecontent
        );
        $filecontent = str_replace(
            $value[self::_CLASSES][self::_OLD], $value[self::_CLASSES][self::_NEW], $filecontent
        );
        $filecontent = str_replace(
            $this->getInstances(self::_OLD), $this->getInstances(self::_NEW), $filecontent
        );
        return $filecontent;
    }

    /**
     * getInstances
     * 
     * @return array
     */
    private function getInstances($key) {
        $instances = [];
        for ($c = 0; $c < count($this->transfo); $c++) {
            $value = $this->transfo[$c];
            $instances[] = $value[self::_INSTANCES][$key];
        }
        return $instances;
    }

    /**
     * setScannedFiles
     * 
     * @return array
     */
    private function setScannedFiles() {
        $this->scanner = new scanner(
            $this->rootDir
            , $excludeDir = [self::_CONFIG]
            , [self::_PHP]
            , $includeDir = false
            , $showDir = false
        );
        $this->scanner->process();
        $this->scannedFiles = $this->scanner->filesScanned;
        return $this;
    }

    /**
     * getTransfo
     * 
     * @param array $scannedFiles
     * @return array
     */
    private function setTransfo() {
        $transItems = [];
        foreach ($this->scannedFiles as $value) {
            if (basename($value) !== 'index.php') {
                $transItems[] = [
                    self::_FILES => [
                        self::_OLD => $value,
                        self::_NEW => $this->getNewPath($value)
                    ],
                    self::_NAMESPACES => [
                        self::_OLD => $this->getTransItem($value, null, self::PARAM_NAMESPACE, '')
                        , self::_NEW => $this->getTransItem($value, self::PARAM_UPPER, self::PARAM_NAMESPACE, '')
                    ],
                    self::_CLASSES => [
                        self::_OLD => $this->getTransItem($value, null, 'class ', '', true)
                        , self::_NEW => $this->getTransItem($value, self::PARAM_UPPER, 'class ', '', true)
                    ],
                    self::_INSTANCES => [
                        self::_OLD => $this->getTransItem($value, null) . self::BACkSLASH 
                            . str_replace(self::PHP_EXT, '', basename($value))
                        , self::_NEW => $this->getTransItem($value, self::PARAM_UPPER) . self::BACkSLASH
                            . str_replace(self::PHP_EXT, '', ucfirst(basename($value)))
                    ]
                ];
            }
        }
        $this->transfo = $transItems;
        return $this;
    }

    /**
     * getNewPath
     * 
     * @param string $rootDir
     * @param string $value
     * @return string
     */
    private function getNewPath($value) {
        $relativeDir = str_replace($this->rootDir, '', $value);
        $namespaceParts = explode(self::SLASH, $relativeDir);
        $namespaceUpParts = array_map(self::PARAM_UPPER, $namespaceParts);
        $namespaceUpped = implode(self::SLASH, $namespaceUpParts);
        return $this->rootDir . $namespaceUpped;
    }

    /**
     * getTransItem
     * 
     * @param string $value
     * @param string $callback
     * @param string $prefix
     * @param string $suffix
     * @param boolean $lastOnly
     * @return type
     */
    private function getTransItem($value, $callback = null, $prefix = '', $suffix = '', $lastOnly = false) {
        if ($lastOnly) {
            $value = str_replace(self::PHP_EXT, '', $value);
            $namespaceUpped = ($callback) ? $callback(basename($value)) : basename($value);
        } else {
            $relativeDir = str_replace($this->rootDir, '', $value);
            $namespacify = str_replace(self::SLASH, self::BACkSLASH, $relativeDir);
            $namespaceParts = explode(self::BACkSLASH, $namespacify);
            array_pop($namespaceParts);
            if ($callback) {
                $namespaceParts = array_map($callback, $namespaceParts);
            }
            $namespaceUpped = implode(self::BACkSLASH, $namespaceParts);
        }
        return $prefix . $namespaceUpped . $suffix;
    }

    /**
     * asJson
     * 
     * @param mixed $content
     * @return \pimvc\http\response
     */
    private function asJson($content) {
        return $this->getApp()->getResponse()
            ->setContent($content)
            ->setType(\pimvc\http\response::TYPE_JSON)
            ->setHttpCode(200);
    }
}