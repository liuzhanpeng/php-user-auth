<?php

namespace Lzpeng\Auth\Tests;

use Lzpeng\Auth\Access\AccessorInterface;
use Lzpeng\Auth\Authenticators\AbstractAuthenticator;
use Lzpeng\Auth\Event\EventManagerInterface;
use Lzpeng\Auth\Exception\AuthException;
use Lzpeng\Auth\Exception\Exception;
use Lzpeng\Auth\Exception\InvalidCredentialException;
use Lzpeng\Auth\UserInterface;
use Lzpeng\Auth\UserProviderInterface;
use Lzpeng\Auth\Users\GenericUser;
use PHPUnit\Framework\TestCase;

class AbstractAuthenticatorTest extends TestCase
{
    private $authenticator;

    protected function setUp(): void
    {
        $this->authenticator = $this->getMockForAbstractClass(AbstractAuthenticator::class);
    }

    public function testSetEventManager()
    {
        $eventManager = $this->getMockBuilder(EventManagerInterface::class)
            ->getMock();

        $this->authenticator->setEventManager($eventManager);

        return $this->authenticator;
    }

    /**
     * @depends clone testSetEventManager
     */
    public function testLoginWithUserProviderException($authenticator)
    {
        $userProvider = $this->getMockBuilder(UserProviderInterface::class)->getMock();

        $userProvider->method('findByCredentials')
            ->will($this->throwException(new InvalidCredentialException('密码错误')));

        $authenticator->setUserProvider($userProvider);

        $this->expectException(InvalidCredentialException::class);
        $this->expectExceptionMessage('密码错误');
        $authenticator->login([
            'name' => 'peng',
            'password' => '123654',
        ]);
    }

    /**
     * @depends clone testSetEventManager
     */
    public function testLoginWithUserProviderNullUser($authenticator)
    {
        $userProvider = $this->getMockBuilder(UserProviderInterface::class)
            ->getMock();

        $authenticator->setUserProvider($userProvider);

        $this->expectException(InvalidCredentialException::class);
        $this->expectExceptionMessage('无效的用户凭证');
        $authenticator->login([
            'name' => 'peng',
            'password' => '123654',
        ]);
    }

    /**
     * @depends clone testSetEventManager
     */
    public function testLoginValidateFailure($authenticator)
    {
        $userProvider = $this->getMockBuilder(UserProviderInterface::class)
            ->getMock();

        $userProvider->method('findByCredentials')
            ->willReturn(new GenericUser([
                'id' => 1,
                'name' => 'peng',
                'password' => '****',
            ]));
        $userProvider->method('validateCredentials')
            ->will($this->throwException(new AuthException('验证失败')));

        $this->expectException(AuthException::class);
        $this->expectExceptionMessage('验证失败');

        $authenticator->setUserProvider($userProvider);

        $authenticator->login([
            'name' => 'peng',
            'password' => '123654',
        ]);
    }

    /**
     * @depends clone testSetEventManager
     */
    public function testLogin($authenticator)
    {
        $userProvider = $this->getMockBuilder(UserProviderInterface::class)
            ->getMock();

        $userProvider->method('findByCredentials')
            ->willReturn(new GenericUser([
                'id' => 1,
                'name' => 'peng',
                'password' => '123654',
            ]));

        $authenticator->method('storeUser')
            ->willReturn('success');
        $authenticator->setUserProvider($userProvider);

        $result = $authenticator->login([
            'name' => 'peng',
            'password' => '123654',
        ]);
        $this->assertEquals('success', $result);

        return $authenticator;
    }

    /**
     * @depends clone testSetEventManager
     */
    public function testUserInfoWithoutLogin($authenticator)
    {
        $this->authenticator->method('loadUser')
            ->willReturn(null);

        $this->assertNull($authenticator->user());
        $this->assertFalse($authenticator->isLogined());
        $this->assertNull($authenticator->id());
    }

    /**
     * @depends clone testSetEventManager
     */
    public function testUserInfoWithLogin($authenticator)
    {
        $authenticator->method('loadUser')
            ->willReturn(new GenericUser([
                'id' => 1,
                'name' => 'peng',
                'password' => '****',
            ]));

        $this->assertInstanceOf(UserInterface::class, $authenticator->user());
        $this->assertTrue($authenticator->isLogined());
        $this->assertEquals(1, $authenticator->id());
    }

    /**
     * @depends clone testSetEventManager
     */
    public function testSetUserWithException($authenticator)
    {
        $authenticator->method('storeUser')
            ->will($this->throwException(new AuthException('error')));

        $this->expectException(AuthException::class);
        $authenticator->setUser(new GenericUser([
            'id' => 1,
            'name' => 'peng',
            'password' => '123654',
        ]));
    }

    /**
     * @depends clone testSetEventManager
     */
    public function testSetUser($authenticator)
    {
        $authenticator->method('storeUser')
            ->willReturn('success');

        $result = $authenticator->setUser(new GenericUser([
            'id' => 1,
            'name' => 'peng',
            'password' => '123654',
        ]));

        $this->assertEquals('success', $result);
    }

    /**
     * @depends clone testSetEventManager
     */
    public function testLogout($authenticator)
    {
        $authenticator->method('loadUser')
            ->willReturn(new GenericUser([
                'id' => 1,
                'name' => 'peng',
                'password' => '****',
            ]));

        $authenticator->logout();
    }

    public function testIsAllowWithoutLogin()
    {
        $result = $this->authenticator->isAllowed('resource1');
        $this->assertFalse($result);
    }

    /**
     * @depends clone testSetEventManager
     */
    public function testIsAllowedWithoutAccessor($authenticator)
    {
        $authenticator->method('loadUser')
            ->willReturn(new GenericUser([
                'id' => 1,
                'name' => 'peng',
                'password' => '123654',
            ]));

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('认证器未设置访问控制器');
        $authenticator->isAllowed('resource1');
    }

    /**
     * @depends clone testSetEventManager
     */
    public function testIsAllowed($authenticator)
    {
        $accessor = $this->getMockBuilder(AccessorInterface::class)->getMock();
        $accessor->method('isAllowed')->willReturn(true);

        $authenticator->setAccessor($accessor);

        $authenticator->method('loadUser')
            ->willReturn(new GenericUser([
                'id' => 1,
                'name' => 'peng',
                'password' => '123654',
            ]));

        $this->assertTrue($authenticator->isAllowed('resource1'));
    }
}
