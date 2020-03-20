<?php

return [
    'default' => 'test',

    'authenticators' => [
        'test' => [
            'driver' => 'test_authenticator_driver',
            'params' => [
                'session_key' => 'UserIdentity',
            ],
            'provider' => [
                'driver' => 'test_provider_driver',
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
            'events' => [
                'login_before' => [
                    Lzpeng\Auth\Tests\TestListener::class,
                ]
            ],
        ],
        'test2' => [
            'driver' => 'test_authenticator_driver',
            'provider' => [
                'driver' => 'test_provider_driver',
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
                'session_key' => 'UserIdentity',
            ],
        ],

        'test3' => [
            'driver' => 'test_authenticator_driver',
            'provider' => [
                'driver' => 'test_provider_driver',
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
                'session_key' => 'UserIdentity',
            ],
            'access' => [
                'provider' => [
                    'driver' => 'test_access_resource_provider',
                ],
                'events' => [
                    'access_success' => [
                        Lzpeng\Auth\Tests\TestListener::class,
                    ]
                ]
            ],
        ],
    ]

];
