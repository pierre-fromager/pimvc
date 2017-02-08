<?php

namespace lib\controller\interfaces;

use lib\app;

/**
 * Description of basic
 *
 * @author Pierre Fromager
 */
interface basicInterface {

    /**
     * __construct
     * 
     * @param \lib\app $app
     * @param type $params
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
