<?php

return array(
    'routes' => [
        '/^(home)$/', // 1st group match controller with default action
        '/^(home)\/(.*?)(\?.*)/', // 3rd group match ?a=1&b=2
        '/^(home)\/(.*?)(\/.*)/', // 3rd group match /a/1/b/2
        '/^(home)\/(.*)$/', // 1st group match controller 2nd match action
        '/^(stat)$/',
        '/^(stat)\/([a-zA-Z0-9_]{1,10})/',
        '/^api\/v1\/([a-zA-Z0-9_]{1,10})/',
        '/^api\/v1\/([a-zA-Z0-9_]{1,10})\/(\d*)/',
    ]
);