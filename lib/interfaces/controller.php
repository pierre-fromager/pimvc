<?php

/**
 * Description of lib\interfaces\controller
 *
 * @author pierrefromager
 */

namespace lib\interfaces;

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

    public function __construct(\lib\app $app = null);

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
