<?php

return [
    'default' => 'test',

    'authenticators' => [
        'test' => [
            'driver' => 'session',
            'provider' => [
                'driver' => 'array',
                'params' => [
                    [
                        'id' => 1,
                        'name' => 'peng',
                        'password' => '123654',
                        'remark' => '测试用户1',
                    ],
                    [
                        'id' => 2,
                        'name' => 'test',
                        'password' => '123654',
                        'remark' => '测试用户2',
                    ],
                ]
            ],
            'params' => [
                'session_key' => 'AdminIdentity',
            ],
        ],
    ]

];
