<?php
/**
 * Description of basicController
 *
 * @author Pierre Fromager
 */
namespace Pimvc\Controller;

abstract class Base implements Interfaces\Base
{

    private $app;

    /**
     * __construct
     *
     * @param \Pimvc\App $app
     */
    public function __construct(\Pimvc\App $app)
    {
        $this->app = $app;
    }

    /**
     * getApp
     *
     * @return \Pimvc\App
     */
    public function getApp()
    {
        return $this->app;
    }
}
