<?php

use Lzpeng\Auth\Authenticators\NativeSessionAuthenticator;
use PHPUnit\Framework\TestCase;
use Lzpeng\Auth\AuthManager;
use Lzpeng\Auth\UserProviders\NativeArrayUserProvider;

class AuthManagerTest extends TestCase
{
    private $authManager;

    public function setUp(): void
    {
        $config = require('auth.php');
        $this->authManager = new AuthManager($config);
    }

    public function testRegisterUserProviderCreator()
    {
        $this->authManager->registerUserProviderCreator('array', function ($config) {
            return new NativeArrayUserProvider($config);
        });
        $this->authManager->registerAuthenticatorCreator('session', function ($config) {
            return new NativeSessionAuthenticator($config['session_key']);
        });

        $authenticator1 = $this->authManager->create();
        $authenticator2 = $this->authManager->create();
        $this->assertSame($authenticator1, $authenticator2, '两个认证器实例不同');

        return $authenticator1;
    }
}
