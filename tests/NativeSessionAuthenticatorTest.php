<?php

namespace Lzpeng\Auth\Tests;

use Lzpeng\Auth\Authenticators\NativeSessionAuthenticator;
use Lzpeng\Auth\Event\EventManagerInterface;
use Lzpeng\Auth\UserInterface;
use Lzpeng\Auth\UserProviderInterface;
use Lzpeng\Auth\Users\GenericUser;
use PHPUnit\Framework\TestCase;

class NativeSessionAuthenticatorTest extends TestCase
{
    private $authenticator;

    protected function setUp(): void
    {
        $this->authenticator = new NativeSessionAuthenticator('UserIdentity');
        $eventManager = $this->getMockBuilder(EventManagerInterface::class)
            ->getMock();

        $this->authenticator->setEventManager($eventManager);
    }

    public function testLogin()
    {
        $userProvider = $this->getMockBuilder(UserProviderInterface::class)->getMock();
        $userProvider->method('findByCredentials')
            ->willReturn(new GenericUser([
                'id' => 1,
                'name' => 'peng',
                'password' => '123654',
            ]));

        $this->authenticator->setUserProvider($userProvider);

        $this->assertFalse($this->authenticator->isLogined());

        $this->authenticator->login([
            'name' => 'peng',
            'password' => '123654',
        ]);

        $this->assertTrue($this->authenticator->isLogined());
        $this->assertInstanceOf(UserInterface::class, $this->authenticator->user());
        $this->assertEquals(1, $this->authenticator->id());

        $this->authenticator->logout();

        $this->assertFalse($this->authenticator->isLogined());
    }
}
