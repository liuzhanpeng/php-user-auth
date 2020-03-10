<?php

namespace Lzpeng\Tests\Creators;

use Lzpeng\Tests\Authenticators\MemoryAuthenticator;
use Lzpeng\Auth\Contracts\AuthenticatorCreatorInterface;

class MemoryAuthenticatorCreator implements AuthenticatorCreatorInterface
{
    public function createAuthenticator(array $config)
    {
        return new MemoryAuthenticator($config['session_key']);
    }
}
