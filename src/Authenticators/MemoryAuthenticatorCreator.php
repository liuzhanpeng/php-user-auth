<?php

namespace Lzpeng\Auth\Authenticators;

use Lzpeng\Auth\AuthenticatorCreatorInterface;

/**
 * 内存认证器创建者
 * 
 * @author lzpeng <liuzhanpeng@gmail.com>
 */
class MemoryAuthenticatorCreator implements AuthenticatorCreatorInterface
{
    public function createAuthenticator(array $config)
    {
        return new MemoryAuthenticator();
    }
}
