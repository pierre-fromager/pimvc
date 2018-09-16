<?php

/**
 * Description of basicInterface
 *
 * @author Pierre Fromager
 */

namespace Pimvc\Controller\Interfaces;

interface Basic extends View
{

    /**
     * __construct
     *
     * @param \Pimvc\App $app
     * @param array $params
     */
    public function __construct(\Pimvc\App $app, $params = []);
}
