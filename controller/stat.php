<?php

/**
 * Description of error
 *
 * @author Pierre Fromager
 */

namespace controller;

class stat {
    
    public function index() {
        return [
            'view' => 'stat',
            'ns' => __NAMESPACE__ ,
            'class' => __CLASS__ ,
            'method' => __METHOD__
        ];
    }
    
    public function home() {
        return [
            'view' => 'stat',
            'ns' => __NAMESPACE__ ,
            'class' => __CLASS__ ,
            'method' => __METHOD__
        ];
    }
}
