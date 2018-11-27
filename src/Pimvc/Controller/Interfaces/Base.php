<?php
/**
 * Description of Base
 *
 * @author pierrefromager
 */
namespace Pimvc\Controller\Interfaces;

interface Base
{

    /**
     * __construct
     *
     * @param \Pimvc\App $app
     */
    public function __construct(\Pimvc\App $app);

    /**
     * getApp
     *
     * @return app
     */
    public function getApp();
}
