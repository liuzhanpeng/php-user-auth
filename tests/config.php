<?php

return [
    'default' => 'test',

    'authenticators' => [
        'test' => [
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
            'events' => [
                // 'login_before' => [
                //     Lzpeng\Tests\Listeners\LogCrendentials::class,
                // ]
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
                'driver' => 'test_access_resource_provider',
            ],
        ],
    ]

];
