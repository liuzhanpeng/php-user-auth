<?php

namespace Lzpeng\Auth\Tests;

use Lzpeng\Auth\AuthenticatorCreatorInterface;
use Lzpeng\Auth\EventableAuthenticatorInterface;
use Lzpeng\Auth\Authenticators\MemoryAuthenticator;
use Lzpeng\Auth\Authenticators\MemoryAuthenticatorCreator;
use Lzpeng\Auth\AuthEventInterface;
use PHPUnit\Framework\TestCase;
use Lzpeng\Auth\AuthManager;
use Lzpeng\Auth\Event\EventManagerCreator;
use Lzpeng\Auth\Exception\ConfigException;
use Lzpeng\Auth\UserProviderCreatorInterface;
use Lzpeng\Auth\UserProviderInterface;
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
        $creator = $this->getMockBuilder(UserProviderCreatorInterface::class)->getMock();
        $provider = $this->getMockBuilder(UserProviderInterface::class)->getMock();
        $creator->method('createUserProvider')
            ->willReturn($provider);

        $this->authManager->registerUserProviderCreator('test_provider_driver', $creator);

        return $this->authManager;
    }

    public function testRegisterUserProviderByClosure()
    {
        $this->authManager->registerUserProviderCreator('test_provider_driver', function ($config) {
            $provider = $this->getMockBuilder(UserProviderInterface::class)->getMock();
            return $provider;
        });

        return $this->authManager;
    }

    /**
     * @depends clone testRegisterUserProviderByClosure
     */
    public function testCreateWithoutRegisterAuthenticator($authManager)
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('找不到认证器驱动[test_authenticator_driver]');
        $authManager->create();
    }

    /**
     * @depends clone testRegisterUserProviderByClosure
     */
    public function testRegisterAuthenticatorByCreator1($authManager)
    {
        $creator = $this->getMockBuilder(AuthenticatorCreatorInterface::class)->getMock();

        $authenticator = $this->getMockBuilder(EventableAuthenticatorInterface::class)->getMock();
        $creator->method('createAuthenticator')
            ->willReturn($authenticator);

        $authManager->registerAuthenticatorCreator('test_authenticator_driver', $creator);

        return $authManager;
    }

    /**
     * @depends clone testRegisterUserProviderByCreator
     */
    public function testRegisterAuthenticatorByCreator2($authManager)
    {
        $creator = $this->getMockBuilder(AuthenticatorCreatorInterface::class)->getMock();

        $authenticator = $this->getMockBuilder(EventableAuthenticatorInterface::class)->getMock();
        $creator->method('createAuthenticator')
            ->willReturn($authenticator);

        $authManager->registerAuthenticatorCreator('test_authenticator_driver', $creator);

        return $authManager;
    }


    /**
     * @depends clone testRegisterUserProviderByClosure
     */
    public function testRegisterAuthenticatorByClosure1($authManager)
    {
        $authManager->registerAuthenticatorCreator('test_authenticator_driver', function ($config) {
            $authenticator = $this->getMockBuilder(EventableAuthenticatorInterface::class)->getMock();
            return $authenticator;
        });

        return $authManager;
    }

    /**
     * @depends clone testRegisterUserProviderByClosure
     */
    public function testRegisterAuthenticatorByClosure2($authManager)
    {
        $authManager->registerAuthenticatorCreator('test_authenticator_driver', function ($config) {
            $authenticator = $this->getMockBuilder(EventableAuthenticatorInterface::class)->getMock();
            return $authenticator;
        });

        return $authManager;
    }

    /**
     * @depends clone testRegisterAuthenticatorByCreator1
     */
    public function testCreateWithWrongName($authManager)
    {
        $this->expectException(ConfigException::class);
        $authManager->create('wrong_name');
    }

    /**
     * @depends clone testRegisterAuthenticatorByCreator1
     */
    public function testCreate1($authManager)
    {
        $authenticator = $authManager->create();
        $this->assertInstanceOf(EventableAuthenticatorInterface::class, $authenticator);

        $authenticator2 = $authManager->create('test');
        $this->assertSame($authenticator, $authenticator2);
    }

    /**
     * @depends clone testRegisterAuthenticatorByCreator2
     */
    public function testCreate2($authManager)
    {
        $authenticator = $authManager->create();
        $this->assertInstanceOf(EventableAuthenticatorInterface::class, $authenticator);
    }

    /**
     * @depends clone testRegisterAuthenticatorByClosure1
     */
    public function testCreate3($authManager)
    {
        $authenticator = $authManager->create();
        $this->assertInstanceOf(EventableAuthenticatorInterface::class, $authenticator);
    }

    /**
     * @depends clone testRegisterAuthenticatorByClosure2
     */
    public function testCreate4($authManager)
    {
        $authenticator = $authManager->create();
        $this->assertInstanceOf(EventableAuthenticatorInterface::class, $authenticator);
    }

    /**
     * @depends clone testRegisterAuthenticatorByCreator1
     */
    public function testCreateWithName($authManager)
    {
        $authenticator = $authManager->create('test');
        $this->assertInstanceOf(EventableAuthenticatorInterface::class, $authenticator);
    }

    /**
     * @depends clone testRegisterAuthenticatorByCreator1
     */
    public function testCreateMultiple($authManager)
    {
        $authenticator1 = $authManager->create('test');
        $this->assertInstanceOf(EventableAuthenticatorInterface::class, $authenticator1);

        $authenticator2 = $authManager->create('test2');
        $this->assertInstanceOf(EventableAuthenticatorInterface::class, $authenticator2);
    }
}
