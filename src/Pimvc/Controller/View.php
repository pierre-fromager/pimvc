<?php

/**
 * Description of basicController
 *
 * @author Pierre Fromager
 */

namespace Pimvc\Controller;

use Pimvc\App;

abstract class View extends Response implements Interfaces\View
{
    
    /**
     * __construct
     *
     * @param App $app
     * @param array $params
     */
    public function __construct(\Pimvc\App $app, $params = [])
    {
        parent::__construct($app, $params);
    }
    
    /**
     * getView
     *
     * @param array $params
     * @param string $viewPath
     * @return View
     */
    public function getView($params, $viewPath)
    {
        $filename = $this->getApp()->getPath() . $viewPath;
        return $this->getApp()
            ->getView()
            ->setParams($params)
            ->setFilename($filename)
            ->render();
    }
}
