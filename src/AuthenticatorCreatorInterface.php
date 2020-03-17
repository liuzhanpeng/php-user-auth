<?php

namespace Lzpeng\Auth;

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
     * @param array $config 认证器配置项
     * @return AuthenticatorInterface
     */
    public function createAuthenticator(array $config);
}
