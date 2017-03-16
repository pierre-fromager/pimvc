<?php

/**
 * Description of pimvc\interfaces\controller
 *
 * @author pierrefromager
 */

namespace pimvc\interfaces;

interface controller {

    const error = 'error';
    const _namespace = 'controller';
    const defaultController = 'home';
    const defaultAction = 'index';
    const baskSlash = '\\';
    const phpExt = '.php';
    const code = 'code';
    const message = 'message';
    const questionMark = '?';

    public function __construct(\pimvc\app $app = null);

    public function setClassPrefix($prefix);

    public function getApp();

    public function setName($name);

    public function setAction($action);

    public function getPath();

    public function check($className);

    public function setDefault();

    public function run();

    public function dispatch();

    public function getParams($key = '');
}
