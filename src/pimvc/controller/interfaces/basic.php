<?php

/**
 * Description of basicInterface
 *
 * @author Pierre Fromager
 */

namespace pimvc\controller\interfaces;

use pimvc\app;

interface basic {

    /**
     * __construct
     * 
     * @param \pimvc\app $app
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
