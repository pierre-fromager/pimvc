<?php

/**
 * Description of pimvc\interfaces\config
 *
 * @author pierrefromager
 */

namespace pimvc\interfaces;

interface config {
    
    const CONFIG_REL_PATH = '../config/';
    const ENV_DEV = 'dev';
    const ENV_TEST = 'test';
    const ENV_INT = 'int';
    const ENV_RPOD = 'prod';
    const CONFIG_ERROR_MISSING = 'Missing config env for ';
    
}
