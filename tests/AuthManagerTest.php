<?php

use Lzpeng\Auth\Authenticators\NativeSessionAuthenticator;
use PHPUnit\Framework\TestCase;
use Lzpeng\Auth\AuthManager;
use Lzpeng\Auth\Contracts\AuthenticatorInterface;
use Lzpeng\Auth\Exceptions\ConfigException;
use Lzpeng\Auth\UserProviders\NativeArrayUserProvider;
use Lzpeng\Tests\Creators\NativeArrayUserProviderCreator;
use Lzpeng\Tests\Creators\NativeSessionAuthenticatorCreator;

class AuthManagerTest extends TestCase
{
    private $authManager;

    public function setUp(): void
    {
        $config = require('config.php');
        $this->authManager = new AuthManager($config);
    }

    public function testCreateWithoutRegisterUserProvider()
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('找不到用户身份提供器驱动[test_provider_driver]');

        $this->authManager->create();
    }

    public function testCreateWithUserProviderByCreator()
    {
        $this->authManager->registerUserProviderCreator('test_provider_driver', new NativeArrayUserProviderCreator());

        return $this->authManager;
    }

    public function testCreateWithUserProviderByCallable()
    {
        $this->authManager->registerUserProviderCreator('test_provider_driver', function ($config) {
            return new NativeArrayUserProvider($config);
        });

        return $this->authManager;
    }

    /**
     * @depends testCreateWithUserProviderByCreator
     */
    public function testCreateWithoutRegisterAuthenticator($authManager)
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('找不到认证器驱动[test_authenticator_driver]');

        $authManager->create();
    }

    /**
     * @depends testCreateWithUserProviderByCreator
     */
    public function testCreateWithAuthenticatorByCreator($authManager)
    {
        $authManager->registerAuthenticatorCreator('test_authenticator_driver', new NativeSessionAuthenticatorCreator());

        return $authManager;
    }

    public function testCreateWithAuthenticatorCallable()
    {
        $this->authManager->registerAuthenticatorCreator('test_authenticator_driver', function ($config) {
            return new NativeSessionAuthenticator($config['session_key']);
        });

        return $this->authManager;
    }

    /**
     * @depends testCreateWithAuthenticatorByCreator
     */
    public function testCreate($authManager)
    {
        $authenticator1 = $authManager->create();
        $this->assertInstanceOf(AuthenticatorInterface::class, $authenticator1);

        $authenticator2 = $authManager->create();
        $this->assertSame($authenticator1, $authenticator2, '单个认证项产生了多个认证器实例');

        return $authenticator1;
    }

    /**
     * @depends testCreateWithAuthenticatorByCreator
     */
    public function testCreateWithWrongName($authManager)
    {
        $this->expectException(ConfigException::class);
        $authManager->create('wrong_name');
    }

    /**
     * @depends testCreateWithAuthenticatorByCreator
     */
    public function testCreateWithName($authManager)
    {
        $authenticator = $authManager->create('test2');

        $this->assertInstanceOf(AuthenticatorInterface::class, $authenticator);
    }
}
