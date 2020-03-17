<?php

namespace Lzpeng\Auth\Tests;

use Lzpeng\Auth\Access\Resource;
use Lzpeng\Auth\Access\AccessResourceProviderInterface;

class ArrayAccessResourceProvider implements AccessResourceProviderInterface
{
    private $config;

    public function __construct()
    {
        $this->config = [
            [
                'userId' => 1,
                'resources' => ['test_resource1', 'test_resource2'],
            ],
            [
                'userId' => 2,
                'resources' => ['test_resource2'],
            ]
        ];
    }

    public function getAccessResources($user)
    {
        $list = [];
        foreach ($this->config as $item) {
            if ($item['userId'] === $user->id()) {
                foreach ($item['resources'] as $resource) {
                    $list[] = new Resource($resource);
                }
            }
        }

        return $list;
    }
}
