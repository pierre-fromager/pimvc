<?php
/**
 * Description of Pimvc\Interfaces\Controller
 *
 * @author pierrefromager
 */
namespace Pimvc\Interfaces;

interface Controller
{

    const ERROR = 'error';
    const _NAMESPACE = 'Controller';
    const DEFAULT_CONTROLER = 'Home';
    const DEFAULT_ACTION = 'Index';
    const BACKSLASH = '\\';
    const PHP_EXT = '.php';
    const CODE = 'code';
    const MESSAGE = 'message';
    const QMARK = '?';

    public function __construct(\Pimvc\App $app = null);

    public function setClassPrefix(string $prefix): \Pimvc\Controller;

    public function getApp(): \Pimvc\App;

    public function setName($name);

    public function setAction($action);

    public function getPath();

    public function check($className): bool;

    public function setDefault();

    public function run();

    public function dispatch();

    public function getParams($key = '');

    public function getControlerClass(): string;

    public function getAction(): string;

    public function setForbidden();
}
