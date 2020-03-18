<?php

namespace Lzpeng\Auth\Tests;

use Lzpeng\Auth\AuthenticatorInterface;
use Lzpeng\Auth\Authenticators\MemoryAuthenticator;
use PHPUnit\Framework\TestCase;
use Lzpeng\Auth\AuthManager;
use Lzpeng\Auth\Event\Event;
use Lzpeng\Auth\Exception\AccessException;
use Lzpeng\Auth\Exception\AuthException;
use Lzpeng\Auth\ResourceProviders\NativeArrayResourceProvider;
use Lzpeng\Auth\UserInterface;
use Lzpeng\Auth\UserProviders\NativeArrayUserProvider;
use Lzpeng\Auth\Users\GenericUser;

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
        $this->assertNull($authenticator->id());
        $this->assertNull($authenticator->user());
    }

    /**
     * @depends testCreateAuthenticator
     */
    public function testSetUser($authenticator)
    {
        $this->assertFalse($authenticator->isLogined());

        $authenticator->setUser(new GenericUser([
            'id' => 1,
            'name' => 'peng',
        ]));

        $this->assertTrue($authenticator->isLogined());
    }

    public function testLoginEventByClosure()
    {
        $authenticator = $this->authManager->create();
        $this->assertFalse($authenticator->isLogined());
        $authenticator->getEventManager()->addListener('login_success', function (Event $event) {
            if ($event->credentials['name'] == 'peng') {
                throw new AuthException('invalid user');
            }
        });

        $this->expectException(AuthException::class);
        $this->expectExceptionMessage('invalid user');

        $authenticator->login([
            'name' => 'peng',
            'password' => '123654',
        ]);
    }

    public function testLoginEventStop()
    {
        $authenticator = $this->authManager->create();

        $authenticator->getEventManager()->addListener('login_before', function (Event $event) {
            $event->stop();
        });
        $authenticator->getEventManager()->addListener('login_before', function (Event $event) {
            throw new AuthException('error');
        });

        $authenticator->login([
            'name' => 'peng',
            'password' => '123654',
        ]);
    }

    public function testIsAllowWithoutLogin()
    {
        $this->authManager->registerAuthenticatorCreator('test_authenticator_driver', function ($config) {
            return new MemoryAuthenticator($config['session_key']);
        });

        $this->authManager->registerResourceProvider('test_access_resource_provider', new NativeArrayResourceProvider([[
            'id' => 1,
            'name' => 'peng',
            'resources' => ['resource1', 'resource2']
        ]]));

        $authenticator = $this->authManager->create('test3');

        $this->expectException(AccessException::class);
        $result = $authenticator->isAllowed('test_resource1');
        $this->assertTrue($result);

        return $authenticator;
    }

    public function testIsAllow()
    {
        $this->authManager->registerAuthenticatorCreator('test_authenticator_driver', function ($config) {
            return new MemoryAuthenticator($config['session_key']);
        });


        $this->authManager->registerResourceProvider('test_access_resource_provider', new NativeArrayResourceProvider([[
            'id' => 1,
            'name' => 'peng',
            'resources' => ['resource1', 'resource2']
        ]]));

        $authenticator = $this->authManager->create('test3');

        $authenticator->login([
            'name' => 'peng',
            'password' => 123654,
        ]);

        $result = $authenticator->isAllowed('resource1');
        $this->assertTrue($result);
        $result = $authenticator->isAllowed('resource2');
        $this->assertTrue($result);
        $result = $authenticator->isAllowed('not my resource');
        $this->assertFalse($result);
    }

    public function testAccessEvent()
    {
        $this->authManager->registerAuthenticatorCreator('test_authenticator_driver', function ($config) {
            return new MemoryAuthenticator($config['session_key']);
        });

        $this->authManager->registerResourceProvider('test_access_resource_provider', new NativeArrayResourceProvider([[
            'id' => 1,
            'name' => 'peng',
            'resources' => ['resource1', 'resource2']
        ]]));

        $authenticator = $this->authManager->create('test3');

        $authenticator->getEventManager()->removeListener('access_after');

        $authenticator->getEventManager()->addListener('access_after', function (Event $event) {
            if (!$event->isAllowed) {
                file_put_contents('access_log', sprintf('%s: %s', $event->user->name, $event->resourceId));
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
