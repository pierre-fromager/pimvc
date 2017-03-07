<?php

return [
    'router' => [
        'unroutable' => '!\.(ico|xml|txt|avi|htm|zip|js|ico|gif|jpg|JPG|png|css|swf|flv|m4v|mp3|mp4|ogv|webm|woff)$'
    ],
    'routes' => [
        '/!\.(ico|xml|txt|avi|htm|zip|js|ico|gif|jpg|JPG|png|css|swf|flv|m4v|mp3|mp4|ogv|webm|woff)$/',
        '/^(home)$/', // 1st group match controller with default action
        '/^(home)\/(.*?)(\?.*)/', // 3rd group match ?a=1&b=2
        '/^(home)\/(.*?)(\/.*)/', // 3rd group match /a/1/b/2
        '/^(home)\/(.*)$/', // 1st group match controller 2nd match action
        '/^(user)$/', // 1st group match controller with default action
        '/^(user)\/(.*?)(\?.*)/', // 3rd group match ?a=1&b=2
        '/^(user)\/(.*?)(\/.*)/', // 3rd group match /a/1/b/2
        '/^(user)\/(.*)$/', // 1st group match controller 2nd match action
        '/^(stat)$/',
        '/^(stat)\/([a-zA-Z0-9_]{1,10})/',
        '/^api\/v1\/([a-zA-Z0-9_]{1,10})/',
        '/^api\/v1\/([a-zA-Z0-9_]{1,10})\/(\d*)/',
    ],
    'dbPool' => [
        'db1' => [
            'adapter' => 'PdoMysql',
            'name' => 'rdmax',
            'host' => '192.168.1.48',
            'user' => 'pierre',
            'port' => '3306',
            'password' => 'pierre'
        ] ,
        'db2' => [
            'adapter' => 'PdoPgsql',
            'name' => 'rdmax',
            'host' => 'localhost',
            'user' => 'pierre',
            'port' => '5432',
            'password' => 'pierre'
        ]
    ],
    'classes' => ['prefix' => 'app1'],
    'html' => [
        'layout' => [
            'title' => 'sample app',
            'doctype' => '<!DOCTYPE html>',
            'serverName' => '',
            'description' => 'sample app description',
            'publisher' => '',
            'revisitafter' => '1 days',
            'robots' => 'all',
            'copyright' => '',
            'organization' => 'Pier-Infor',
            'author' => 'Pierre Fromager',
            'keywords' => 'pimvc',
            'country' => 'France',
            'pocode' => '93300',
            'email' => 'info@pier-infor.fr',
            'street' => '34 bld anatole france',
            'city' => 'Aubervilliers',
            'twitter_link' => '',
            'github_link' => '',
            'linkedin_link' => '',
        ],
        'nav' => [
            'title' => [
                'text' => 'PimVc',
                'icon' => 'fa fa-heart-o',
                'link' => ''
            ],
            'items' => [
                [
                    'title' => '1st title'
                    , 'icon' => 'fa fa-cutlery'
                    , 'link' => '#'
                ],
                [
                    'title' => '2nd title'
                    , 'icon' => 'fa fa-smile'
                    , 'link' => '#'
                ]
            ]
        ],
        'carousel' => [
            'interval' => 3000,
            'items' => [
                [
                    'title' => '1st title'
                    , 'description' => '1st description'
                    , 'image' => 'http://2.bp.blogspot.com/-qngGKFQdxn0/TkPHwQbBrgI/AAAAAAAAEIk/zHBcHDX_qak/s1600/Peugeot907SuperCar.jpg'
                ],
                [
                    'title' => '2nd title'
                    , 'description' => '2nd description'
                    , 'image' => 'http://4.bp.blogspot.com/-EXIcSS-_E5o/Txg98fUagNI/AAAAAAAAAmw/wvXGwKKua6s/s400/super_cars_wallpaper+11.jpg'
                ],
                [
                    'title' => '2nd title'
                    , 'description' => '2nd description'
                    , 'image' => 'http://loadinform.com/wp-content/uploads/2010/06/new-jaguar-supercar.jpeg'
                ],
            ]
        ]
    ]
];