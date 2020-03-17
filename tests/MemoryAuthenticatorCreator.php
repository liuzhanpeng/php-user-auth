<?php

namespace Lzpeng\Auth\Tests;

use Lzpeng\Auth\AuthenticatorCreatorInterface;

class MemoryAuthenticatorCreator implements AuthenticatorCreatorInterface
{
    public function createAuthenticator(array $config)
    {
        return new MemoryAuthenticator($config['session_key']);
    }
}
