<?php

namespace Lzpeng\Auth\UserProviders;

use Lzpeng\Auth\UserProviderCreatorInterface;

/**
 * 原生数组用户身份提供器创建者
 * 
 * @author lzpeng <liuzhanpeng@gmail.com>
 */
class NativeArrayUserProviderCreator implements UserProviderCreatorInterface
{
    /**
     * @inheritDoc
     */
    public function createUserProvider(array $config)
    {
        return new NativeArrayUserProvider($config);
    }
}
