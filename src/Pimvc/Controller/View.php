<?php
/**
 * Description of basicController
 *
 * @author Pierre Fromager
 */
namespace Pimvc\Controller;

abstract class View extends Response implements Interfaces\View
{

    /**
     * __construct
     *
     * @param App $app
     * @param array $params
     */
    public function __construct(\Pimvc\App $app, array $params = [])
    {
        parent::__construct($app, $params);
    }

    /**
     * getView
     *
     * @param array $params
     * @param string $viewPath
     * @return \Pimvc\View
     */
    public function getView(array $params, string $viewPath): \Pimvc\View
    {
        $filename = $this->getApp()->getPath() . $viewPath;
        return $this->getApp()
                ->getView()
                ->setParams($params)
                ->setFilename($filename)
                ->render();
    }
}
