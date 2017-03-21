<?php

/**
 * Description of basicInterface
 *
 * @author Pierre Fromager
 */

namespace Pimvc\Controller\Interfaces;

use Pimvc\App;

interface Basic {

    /**
     * __construct
     * 
     * @param \Pimvc\App $app
     * @param array $params
     */
    public function __construct(app $app, $params);

    /**
     * getApp
     * 
     * @return app
     */
    public function getApp();

    /**
     * getParams
     * 
     * @return array
     */
    public function getParams($key = '');
}
