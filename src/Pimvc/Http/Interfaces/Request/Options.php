<?php

namespace Pimvc\Http\Interfaces\Request;

interface Options
{
    const OPTIONS_REQUEST = 'request';
    const OPTIONS_REQUEST_ERROR_CONFIG = 'Invalid config instance';
    const OPTIONS_HOSTNAME = 'hostname';
    const OPTIONS_SCHEME = 'scheme';

    public function load(\Pimvc\Config $config);
}
