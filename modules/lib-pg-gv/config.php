<?php

return [
    '__name' => 'lib-pg-gv',
    '__version' => '0.0.1',
    '__git' => 'git@github.com:getmim/lib-pg-gv.git',
    '__license' => 'MIT',
    '__author' => [
        'name' => 'Iqbal Fauzi',
        'email' => 'iqbalfawz@gmail.com',
        'website' => 'http://iqbalfn.com/'
    ],
    '__files' => [
        'modules/lib-pg-gv' => ['install','update','remove']
    ],
    '__dependencies' => [
        'required' => [
            [
                'lib-curl' => NULL
            ]
        ],
        'optional' => []
    ],
    'autoload' => [
        'classes' => [
            'LibPgGv\\Library' => [
                'type' => 'file',
                'base' => 'modules/lib-pg-gv/library'
            ]
        ],
        'files' => []
    ],
    'libPgGv' => [
        'base' => 'https://www.gudangvoucher.com'
    ]
];
