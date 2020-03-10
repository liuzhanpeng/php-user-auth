<?php

namespace Lzpeng\Tests;

use Lzpeng\Auth\Authenticators\NativeSessionAuthenticator;
use PHPUnit\Framework\TestCase;
use Lzpeng\Auth\AuthManager;
use Lzpeng\Auth\Contracts\AuthenticatorInterface;
use Lzpeng\Auth\Events\EventManagerCreator;
use Lzpeng\Auth\Exceptions\ConfigException;
use Lzpeng\Auth\UserProviders\NativeArrayUserProvider;
use Lzpeng\Tests\Authenticators\MemoryAuthenticator;
use Lzpeng\Tests\Creators\NativeArrayUserProviderCreator;
use Lzpeng\Tests\Creators\MemoryAuthenticatorCreator;

class AuthManagerTest extends TestCase
{
    private $authManager;

    public function setUp(): void
    {
        $config = require('config.php');
        $this->authManager = new AuthManager($config);
    }

    public function testRegisterUserProviderCreatorByCreator()
    {
        $this->authManager->registerUserProviderCreator('test_provider_driver', new NativeArrayUserProviderCreator());

        return $this->authManager;
    }

    public function testRegisterUserProviderCreatorByClosure()
    {
        $this->authManager->registerUserProviderCreator('test_provider_driver', function ($config) {
            return new NativeArrayUserProvider($config);
        });

        return $this->authManager;
    }

    public function testCreateWithoutRegisterUserProvider()
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('找不到用户身份提供器驱动[test_provider_driver]');

        $this->authManager->create();
    }

    /**
     * @depends testRegisterUserProviderCreatorByClosure
     */
    public function testCreateWithoutRegisterAuthenticator($authManager)
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('找不到认证器驱动[test_authenticator_driver]');

        $authManager->create();
    }

    /**
     * @depends testRegisterUserProviderCreatorByCreator
     */
    public function testCreateWithAuthenticatorByCreator($authManager)
    {
        $authManager->registerAuthenticatorCreator('test_authenticator_driver', new MemoryAuthenticatorCreator());

        return $authManager;
    }

    /**
     * @depends testRegisterUserProviderCreatorByClosure
     */
    public function testCreateWithAuthenticatorClosure($authManager)
    {
        $authManager->registerAuthenticatorCreator('test_authenticator_driver', function ($config) {
            return new MemoryAuthenticator($config['session_key']);
        });

        return $authManager;
    }

    /**
     * @depends testCreateWithAuthenticatorClosure
     */
    public function testCreate($authManager)
    {
        $authenticator1 = $authManager->create();
        $this->assertInstanceOf(AuthenticatorInterface::class, $authenticator1);

        $this->assertFalse($authenticator1->isLogined());

        $authenticator2 = $authManager->create('test');
        $this->assertSame($authenticator1, $authenticator2, '单个认证项产生了多个认证器实例');

        return $authenticator1;
    }

    /**
     * @depends testCreateWithAuthenticatorByCreator
     */
    public function testCreateWithCreator($authManager)
    {
        $authenticator1 = $authManager->create();
        $this->assertInstanceOf(AuthenticatorInterface::class, $authenticator1);

        $authenticator2 = $authManager->create('test');
        $this->assertSame($authenticator1, $authenticator2, '单个认证项产生了多个认证器实例');

        return $authenticator1;
    }

    /**
     * @depends testCreateWithAuthenticatorClosure
     */
    public function testCreateWithWrongName($authManager)
    {
        $this->expectException(ConfigException::class);
        $authManager->create('wrong_name');
    }

    /**
     * @depends testCreateWithAuthenticatorClosure
     */
    public function testCreateWithName($authManager)
    {
        $authenticator = $authManager->create('test2');

        $this->assertInstanceOf(AuthenticatorInterface::class, $authenticator);
    }

    public function testSetEventManagerCreator()
    {
        $this->authManager->setEventManagerCreator(new EventManagerCreator());
    }
}
