<?php

namespace Lzpeng\Tests;

use PHPUnit\Framework\TestCase;
use Lzpeng\Auth\AuthManager;
use Lzpeng\Auth\UserProviders\NativeArrayUserProvider;
use Lzpeng\Auth\Authenticators\NativeSessionAuthenticator;
use Lzpeng\Auth\Contracts\AuthenticatorInterface;
use Lzpeng\Auth\Exceptions\AuthException;

class AuthenticatorTest extends TestCase
{
    private $authManager;

    public function setUp(): void
    {
        $config = require('config.php');
        $this->authManager = new AuthManager($config);

        $this->authManager->registerAuthenticatorCreator('test_authenticator_driver', function ($config) {
            return new NativeSessionAuthenticator($config['session_key']);
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
     *
     * @return void
     */
    public function testLoginWithWrongCrendentials($authenticator)
    {
        $this->expectException(AuthException::class);

        $authenticator->login([
            'name' => 'peng',
            'password' => 'wrong_password',
        ]);
    }
}
