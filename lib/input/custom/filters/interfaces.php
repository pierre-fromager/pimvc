<?php

/**
 * Description of lib\input\custom\filters\interfaces
 *
 * @author pierrefromager
 */

namespace lib\input\custom\filters;

interface interfaces {

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
