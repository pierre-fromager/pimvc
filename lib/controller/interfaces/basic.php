<?php

/**
 * Description of basicInterface
 *
 * @author Pierre Fromager
 */

namespace lib\controller\interfaces;

use lib\app;

interface basicInterface {

    /**
     * __construct
     * 
     * @param \lib\app $app
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
