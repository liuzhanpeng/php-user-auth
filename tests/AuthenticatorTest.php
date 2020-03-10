<?php

namespace Lzpeng\Tests;

use PHPUnit\Framework\TestCase;
use Lzpeng\Auth\AuthManager;
use Lzpeng\Auth\UserProviders\NativeArrayUserProvider;
use Lzpeng\Auth\Contracts\AuthenticatorInterface;
use Lzpeng\Auth\Exceptions\AuthException;
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

    public function testCreateAuthenticator()
    {
        $authenticator = $this->authManager->create();

        $this->assertInstanceOf(AuthenticatorInterface::class, $authenticator);

        return $authenticator;
    }

    /**
     * @depends testCreateAuthenticator
     */
    public function testLoginWithWrongCrendentials($authenticator)
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

        $this->assertTrue($authenticator->isLogined());

        $this->assertEquals(1, $authenticator->id());

        $this->assertEquals('测试用户1', $authenticator->user()->remark);
    }

    /**
     * @depends testCreateAuthenticator
     */
    public function testLogout($authenticator)
    {
        $authenticator->login([
            'name' => 'peng',
            'password' => '123654',
        ]);

        $this->assertTrue($authenticator->isLogined());

        $authenticator->logout();

        $this->assertFalse($authenticator->isLogined());
    }
}
