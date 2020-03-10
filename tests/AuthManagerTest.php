<?php

use Lzpeng\Auth\Authenticators\NativeSessionAuthenticator;
use PHPUnit\Framework\TestCase;
use Lzpeng\Auth\AuthManager;
use Lzpeng\Auth\Exceptions\ConfigException;
use Lzpeng\Auth\UserProviders\NativeArrayUserProvider;

class AuthManagerTest extends TestCase
{
    private $authManager;

    public function setUp(): void
    {
        $config = require('auth.php');
        $this->authManager = new AuthManager($config);
    }

    public function testCreate()
    {
        $this->authManager->registerUserProviderCreator('test_provider_driver', function ($config) {
            return new NativeArrayUserProvider($config);
        });
        $this->authManager->registerAuthenticatorCreator('test_authenticator_driver', function ($config) {
            return new NativeSessionAuthenticator($config['session_key']);
        });

        $authenticator1 = $this->authManager->create();
        $authenticator2 = $this->authManager->create();
        $this->assertSame($authenticator1, $authenticator2, '两个认证器实例不同');

        return $authenticator1;
    }

    public function testCreateWithNotRegisterUserProvider()
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('找不到用户身份提供器驱动[test_provider_driver]');

        $this->authManager->create();
    }

    public function testCreateWithNotRegisterAuthenticator()
    {
        $this->authManager->registerUserProviderCreator('test_provider_driver', function ($config) {
            return new NativeArrayUserProvider($config);
        });

        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('找不到认证器驱动[test_authenticator_driver]');

        $this->authManager->create();
    }
}
