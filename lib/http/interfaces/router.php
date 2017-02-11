<?php

namespace lib\http\interfaces;

interface routerInterface {

    public function __construct($routes);
    public function getUri();
    public function getFragments();
    public function compile();
}