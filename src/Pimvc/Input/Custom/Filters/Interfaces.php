<?php

/**
 * Description of Pimvc\Input\Custom\Filters\Interfaces
 *
 * @author pierrefromager
 */

namespace Pimvc\Input\Custom\Filters;

interface Interfaces {

    const MIN_RANGE = 'min_range';
    const MAX_RANGE = 'max_range';
    const STEP = 'step';
    const STRICT = 'strict';
    const CAST = 'cast';
    const _DEFAULT = 'default';
    const VALUES = 'values';
    const FILTER_STEP_RANGE = 'stepRange';
    const FILTER_ENUM = 'enum';
    const FILTER_INTEGER = 'integer';

    public function __construct($options);

    public function isValid($val);
}
