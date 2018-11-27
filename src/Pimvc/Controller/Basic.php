<?php
/**
 * Description of Pimvc\Controller\Basic
 *
 * @author Pierre Fromager
 */
namespace Pimvc\Controller;

abstract class Basic extends View implements Interfaces\Basic
{

    /**
     * __construct
     *
     * @param \Pimvc\App $app
     * @param array $params
     */
    public function __construct(\Pimvc\App $app, array $params = [])
    {
        parent::__construct($app, $params);
    }
}
