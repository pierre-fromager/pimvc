<?php

/**
 * Description of Pimvc\Interfaces\Controller
 *
 * @author pierrefromager
 */

namespace Pimvc\Interfaces;

interface Controller {

    const error = 'error';
    const _namespace = 'Controller';
    const defaultController = 'Home';
    const defaultAction = 'Index';
    const baskSlash = '\\';
    const phpExt = '.php';
    const code = 'code';
    const message = 'message';
    const questionMark = '?';

    public function __construct(\Pimvc\App $app = null);

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
