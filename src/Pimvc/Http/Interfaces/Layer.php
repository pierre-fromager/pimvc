<?php

namespace Pimvc\Http\Interfaces;

use \Closure;

interface Layer {
    public function peel($object, Closure $next);
}