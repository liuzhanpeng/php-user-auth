<?php

namespace Lzpeng\Auth\Contracts;

/**
 * 认证器创建者接口
 * 
 * @author lzpeng <liuzhanpeng@gmail.com>
 */
interface AuthenticatorCreatorInterface
{
    /**
     * 创建并返回认证器
     *
     * @param UserProviderInterface $userProvider 用户身份对象提供器
     * @param array $config 认证器配置项
     * @return AuthenticatorInterface
     */
    public function createAuthenticator(UserProviderInterface $userProvider, array $config);
}
