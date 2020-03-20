<?php

namespace Lzpeng\Auth\Tests;

use Lzpeng\Auth\Access\ResourceInterface;
use Lzpeng\Auth\ResourceProviders\NativeArrayResourceProvider;
use Lzpeng\Auth\UserInterface;
use Lzpeng\Auth\Users\GenericUser;
use PHPUnit\Framework\TestCase;

class NativeArrayResourceProviderTest extends TestCase
{
    private $config;

    protected function setUp(): void
    {
        $this->config = [
            [
                'id' => 1,
                'name' => 'peng',
                'resources' => [
                    'resource1', 'resource2',
                ],
            ]
        ];
    }

    public function testConstruct()
    {
        $provider = new NativeArrayResourceProvider($this->config);

        return $provider;
    }

    /**
     * @depends testConstruct
     */
    public function testGetResources($provider)
    {
        $user = $this->getMockBuilder(UserInterface::class)
            ->getMock();

        $user->method('id')
            ->willReturn(1);

        $resources = $provider->getResources($user);

        $this->assertCount(2, $resources);

        $this->assertInstanceOf(ResourceInterface::class, $resources[0]);
        $this->assertInstanceOf(ResourceInterface::class, $resources[1]);
    }
}
