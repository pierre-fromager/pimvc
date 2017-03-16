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

    private $scanner;

    /**
     * init
     * 
     */
    protected function init() {
        $where = __DIR__ . '/../../';
        $this->scanner = new scanner($where, [], ['php'], $includeDir = false, $showDir = false);
        $this->scanner->process();
    }

    /**
     * user
     * 
     * @return \pimvc\http\response
     */
    public function index() {
        return $this->asJson($this->scanner);
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
