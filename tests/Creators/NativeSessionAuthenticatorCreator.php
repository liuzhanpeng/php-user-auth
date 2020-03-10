<?php

namespace Lzpeng\Tests\Creators;

use Lzpeng\Auth\Authenticators\NativeSessionAuthenticator;
use Lzpeng\Auth\Contracts\AuthenticatorCreatorInterface;

class NativeSessionAuthenticatorCreator implements AuthenticatorCreatorInterface
{
    public function createAuthenticator(array $config)
    {
        return new NativeSessionAuthenticator($config['session_key']);
    }
}
