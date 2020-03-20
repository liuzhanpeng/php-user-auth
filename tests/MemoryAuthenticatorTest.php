<?php

namespace Lzpeng\Auth\Tests;

use Lzpeng\Auth\Authenticators\MemoryAuthenticator;
use Lzpeng\Auth\Event\EventManagerInterface;
use Lzpeng\Auth\Exception\AuthException;
use Lzpeng\Auth\Exception\InvalidCredentialException;
use Lzpeng\Auth\UserInterface;
use Lzpeng\Auth\UserProviderInterface;
use Lzpeng\Auth\Users\GenericUser;
use PHPUnit\Framework\TestCase;

class MemoryAuthenticatorTest extends TestCase
{
    private $authenticator;

    protected function setUp(): void
    {
        $this->authenticator = new MemoryAuthenticator();
        $eventManager = $this->getMockBuilder(EventManagerInterface::class)
            ->getMock();

        $this->authenticator->setEventManager($eventManager);
    }

    public function testLoginWithInvalidCredential()
    {
        $userProvider = $this->getMockBuilder(UserProviderInterface::class)->getMock();

        $userProvider->method('findByCredentials')
            ->will($this->throwException(new InvalidCredentialException('密码错误')));

        $this->authenticator->setUserProvider($userProvider);

        $this->expectException(AuthException::class);
        $this->authenticator->login([
            'name' => 'peng',
            'password' => '123654',
        ]);
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
