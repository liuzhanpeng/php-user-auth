<?php

namespace Lzpeng\Auth\Tests;

use Lzpeng\Auth\AuthenticatorInterface;
use PHPUnit\Framework\TestCase;
use Lzpeng\Auth\AuthManager;
use Lzpeng\Auth\Event\EventManagerCreator;
use Lzpeng\Auth\Exception\ConfigException;
use Lzpeng\Auth\UserProviders\NativeArrayUserProvider;
use Lzpeng\Auth\UserProviders\NativeArrayUserProviderCreator;

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

    public function testRegisterUserProviderByCreator()
    {
        $this->authManager->registerUserProviderCreator('test_provider_dirver', new NativeArrayUserProviderCreator());

        return $this->authManager;
    }

    public function testRegisterUserProviderByClosure()
    {
        $this->authManager->registerUserProviderCreator('test_provider_driver', function ($config) {
            return new NativeArrayUserProvider($config);
        });

        return $this->authManager;
    }

    /**
     * @depends testRegisterUserProviderByClosure
     */
    public function testCreateWithoutRegisterAuthenticator($authManager)
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('找不到认证器驱动[test_authenticator_driver]');
        $authManager->create();
    }

    /**
     * @depends testRegisterUserProviderByClosure
     */
    public function testRegisterAuthenticatorByCreator($authManager)
    {
        $authManager->registerAuthenticatorCreator('test_authenticator_driver', new MemoryAuthenticatorCreator());

        return $authManager;
    }

    /**
     * @depends testRegisterUserProviderByClosure
     */
    public function testRegisterAuthenticatorByClosure($authManager)
    {
        $authManager->registerAuthenticatorCreator('test_authenticator_driver', function ($config) {
            return new MemoryAuthenticator($config['session_key']);
        });

        return $authManager;
    }

    /**
     * @depends testRegisterAuthenticatorByClosure
     */
    public function testCreateWithWrongName($authManager)
    {
        $this->expectException(ConfigException::class);
        $authManager->create('wrong_name');
    }

    /**
     * @depends testRegisterAuthenticatorByClosure
     */
    public function testCreate($authManager)
    {
        $authenticator = $authManager->create();
        $this->assertInstanceOf(AuthenticatorInterface::class, $authenticator);

        $authenticator2 = $authManager->create('test');
        $this->assertSame($authenticator, $authenticator2);
    }

    /**
     * @depends testRegisterAuthenticatorByClosure
     */
    public function testCreateWithName($authManager)
    {
        $authenticator = $authManager->create('test');
        $this->assertInstanceOf(AuthenticatorInterface::class, $authenticator);
    }

    /**
     * @depends testRegisterAuthenticatorByClosure
     */
    public function testCreateMultiple($authManager)
    {
        $authenticator1 = $authManager->create('test');
        $this->assertInstanceOf(AuthenticatorInterface::class, $authenticator1);

        $authenticator2 = $authManager->create('test2');
        $this->assertInstanceOf(AuthenticatorInterface::class, $authenticator2);
    }

    public function testSetEventManagerCreator()
    {
        $this->authManager->setEventManagerCreator(new EventManagerCreator());
    }
}
