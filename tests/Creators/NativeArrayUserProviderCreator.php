<?php

namespace Lzpeng\Tests\Creators;

use Lzpeng\Auth\Contracts\UserProviderCreatorInterface;
use Lzpeng\Auth\UserProviders\NativeArrayUserProvider;

class NativeArrayUserProviderCreator implements UserProviderCreatorInterface
{
    public function createUserProvider(array $config)
    {
        return new NativeArrayUserProvider($config);
    }
}
