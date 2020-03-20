<?php

namespace Lzpeng\Auth\Tests;

use Lzpeng\Auth\Access\Accessor;
use Lzpeng\Auth\Access\ResourceInterface;
use Lzpeng\Auth\Access\ResourceProviderInterface;
use Lzpeng\Auth\Event\EventManagerInterface;
use Lzpeng\Auth\Exception\Exception;
use Lzpeng\Auth\UserInterface;
use PHPUnit\Framework\TestCase;

class AccessorTest extends TestCase
{
    private $accessor;

    protected function setUp(): void
    {
        $this->accessor = new Accessor();
    }

    public function testWithoutEventManager()
    {
        $user = $this->getMockBuilder(UserInterface::class)->getMock();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('认证器未设置事件管理器');
        $this->accessor->isAllowed($user, 'resource1');
    }

    public function testSetEventManager()
    {
        $eventManager = $this->getMockBuilder(EventManagerInterface::class)->getMock();

        $this->accessor->setEventManager($eventManager);

        return $this->accessor;
    }

    /**
     * @depends testSetEventManager
     */
    public function testWithoutResourceProvider($accessor)
    {
        $user = $this->getMockBuilder(UserInterface::class)->getMock();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('认证器未设置权限资源提供器');
        $accessor->isAllowed($user, 'resource1');
    }

    /**
     * @depends testSetEventManager
     */
    public function testSetResourceProvider($accessor)
    {
        $provider = $this->getMockBuilder(ResourceProviderInterface::class)->getMock();

        $accessor->setResourceProvider($provider);
    }

    /**
     * @depends testSetEventManager
     */
    public function testAllowed($accessor)
    {
        $resource1 = $this->getMockBuilder(ResourceInterface::class)->getMock();
        $resource1->method('id')->willReturn('resource1');

        $resource2 = $this->getMockBuilder(ResourceInterface::class)->getMock();
        $resource2->method('id')->willReturn('resource2');

        $provider = $this->getMockBuilder(ResourceProviderInterface::class)->getMock();
        $provider->method('getResources')
            ->willReturn([
                $resource1, $resource2,
            ]);
        $accessor->setResourceProvider($provider);

        $user = $this->getMockBuilder(UserInterface::class)->getMock();

        $this->assertTrue($accessor->isAllowed($user, 'resource1'));
        $this->assertFalse($accessor->isAllowed($user, 'private'));
    }
}
