<?php
/**
 * Description of basicInterface
 *
 * @author Pierre Fromager
 */
namespace Pimvc\Controller\Interfaces;

interface View extends Response
{

    const VIEW_PATH = '/Views/';

    /**
     * __construct
     *
     * @param \Pimvc\App $app
     * @param array $params
     */
    public function __construct(\Pimvc\App $app, array $params = []);

    /**
     * getView
     *
     * @param array $params
     * @param string $viewPath
     */
    public function getView(array $params, string $viewPath);
}
