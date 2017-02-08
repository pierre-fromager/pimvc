<?php

/**
 * Description of home controller
 *
 * @author Pierre Fromager
 */

namespace controller;

use lib\response;

class home {
    
    public function index() {
        return [
            'view' => 'home',
            'ns' => __NAMESPACE__ ,
            'class' => __CLASS__ ,
            'method' => __METHOD__
        ];
    }
    
    public function home() {
        return [
            'view' => 'home',
            'ns' => __NAMESPACE__ ,
            'class' => __CLASS__ ,
            'method' => __METHOD__
        ];
    }
    
    public function json() {
        $content = [
            'view' => 'home',
            'ns' => __NAMESPACE__ ,
            'class' => __CLASS__ ,
            'method' => __METHOD__
        ];
        return (new response())
            ->setContent(json_encode($content))
            ->setType(response::TYPE_JSON)
            ->setHttpCode(200);
    }
}
