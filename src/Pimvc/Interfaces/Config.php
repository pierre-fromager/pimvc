<?php

/**
 * Description of Pimvc\Interfaces\Config
 *
 * @author pierrefromager
 */

namespace Pimvc\Interfaces;

interface Config
{
    const CONFIG_REL_PATH = '../config/';
    const ENV_DEV = 'dev';
    const ENV_TEST = 'test';
    const ENV_INT = 'int';
    const ENV_PROD = 'prod';
    const ENV_CLI = 'cli';

    const CONFIG_ERROR_MISSING = 'Missing config env for ';

    public function setEnv($env = self::ENV_DEV);

    public function setPath(string $path): \Pimvc\Config;

    public function getPath(): string;

    public function getSettings(string $key = '');

    public function hasEntry(string $key);

    public function load();
}
