<?php

namespace Lzpeng\Tests;

use PHPUnit\Framework\TestCase;
use Lzpeng\Auth\AuthManager;
use Lzpeng\Auth\Contracts\AccessInterface;
use Lzpeng\Auth\UserProviders\NativeArrayUserProvider;
use Lzpeng\Auth\Contracts\AuthenticatorInterface;
use Lzpeng\Auth\Contracts\UserInterface;
use Lzpeng\Auth\Exceptions\AccessException;
use Lzpeng\Auth\Exceptions\AuthException;
use Lzpeng\Tests\Access\ArrayAccessResourceProvider;
use Lzpeng\Tests\Authenticators\MemoryAuthenticator;

class AuthenticatorTest extends TestCase
{
    private $authManager;

    public function setUp(): void
    {
        $config = require('config.php');
        $this->authManager = new AuthManager($config);

        $this->authManager->registerAuthenticatorCreator('test_authenticator_driver', function ($config) {
            return new MemoryAuthenticator($config['session_key']);
        });

        $this->authManager->registerUserProviderCreator('test_provider_driver', function ($config) {
            return new NativeArrayUserProvider($config);
        });
    }

    public function tearDown(): void
    {
        @unlink('log');
        @unlink('access_log');
    }

    public function testCreateAuthenticator()
    {
        $authenticator = $this->authManager->create();

        $this->assertInstanceOf(AuthenticatorInterface::class, $authenticator);

        return $authenticator;
    }

    /**
     * @depends testCreateAuthenticator
     */
    public function testLoginWithCrendentialsByWrongName($authenticator)
    {
        $this->expectException(AuthException::class);

        $authenticator->login([
            'name' => 'wrong_name',
            'password' => 'wrong_password',
        ]);
    }

    /**
     * @depends testCreateAuthenticator
     */
    public function testLoginWithCrendentialsByWrongPassword($authenticator)
    {
        $this->expectException(AuthException::class);

        $authenticator->login([
            'name' => 'peng',
            'password' => 'wrong_password',
        ]);
    }

    /**
     * @depends testCreateAuthenticator
     */
    public function testLogin($authenticator)
    {
        $this->assertFalse($authenticator->isLogined());

        $authenticator->login([
            'name' => 'peng',
            'password' => '123654',
        ]);

        return $authenticator;
    }

    /**
     * @depends testLogin
     */
    public function testIsLogined($authenticator)
    {
        $this->assertTrue($authenticator->isLogined());
    }

    /**
     * @depends testLogin
     */
    public function testGetId($authenticator)
    {
        $this->assertEquals(1, $authenticator->id());
    }

    /**
     * @depends testLogin
     */
    public function testGetUser($authenticator)
    {
        $this->assertInstanceOf(UserInterface::class, $authenticator->user());
        $this->assertEquals('测试用户1', $authenticator->user()->remark);
    }

    /**
     * @depends testLogin
     */
    public function testLogout($authenticator)
    {
        $this->assertTrue($authenticator->isLogined());

        $authenticator->logout();

        $this->assertFalse($authenticator->isLogined());
    }

    /**
     * @depends testCreateAuthenticator
     */
    public function testLoginEventClosure($authenticator)
    {
        $this->assertFalse($authenticator->isLogined());
        $authenticator->getEventManager()->attachListener('login_before', function ($arg) {
            throw new AuthException('testerror');
        });

        $this->expectException(AuthException::class);
        $this->expectExceptionMessage('testerror');

        $authenticator->login([
            'name' => 'peng',
            'password' => '123654',
        ]);
    }

    /**
     * @depends testCreateAuthenticator
     */
    public function testLoginEventListener($authenticator)
    {
        $this->assertFalse($authenticator->isLogined());

        $authenticator->getEventManager()->detachListener('login_before');
        $authenticator->getEventManager()->attachListener('login_before', new \Lzpeng\Tests\Listeners\LogCrendentials());

        $authenticator->login([
            'name' => 'peng',
            'password' => '123654',
        ]);

        $this->assertFileExists('log');
    }

    public function testIsAllowWithoutLogin()
    {
        $this->authManager->registerAccessResourceProvider('test_access_resource_provider', new ArrayAccessResourceProvider());

        $authenticator = $this->authManager->create('test3');

        $this->expectException(AccessException::class);
        $result = $authenticator->isAllowed('test_resource1');
        $this->assertTrue($result);

        return $authenticator;
    }

    public function testIsAllow()
    {
        $this->authManager->registerAccessResourceProvider('test_access_resource_provider', new ArrayAccessResourceProvider());

        $authenticator = $this->authManager->create('test3');

        $authenticator->login([
            'name' => 'peng',
            'password' => 123654,
        ]);

        $result = $authenticator->isAllowed('test_resource1');
        $this->assertTrue($result);
        $result = $authenticator->isAllowed('test_resource2');
        $this->assertTrue($result);
        $result = $authenticator->isAllowed('not my resource');
        $this->assertFalse($result);
    }

    public function testAccessEvent()
    {
        $this->authManager->registerAccessResourceProvider('test_access_resource_provider', new ArrayAccessResourceProvider());

        $authenticator = $this->authManager->create('test3');

        $authenticator->getEventManager()->detachListener('access_after');

        $authenticator->getEventManager()->attachListener('access_after', function ($params) {
            if ($params['isAllowed'] == 0) {
                file_put_contents('access_log', sprintf('%s: %s', $params['user']->name, $params['resourceId']));
            }
        });

        $authenticator->login([
            'name' => 'peng',
            'password' => 123654,
        ]);

        $result = $authenticator->isAllowed('test_resourcexxxxxx');
        $this->assertFalse($result);

        $this->assertFileExists('access_log');
    }
}
