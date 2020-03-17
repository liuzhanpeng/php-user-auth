<?php

namespace Lzpeng\Auth;

/**
 * 用户身份对象提供器创建者接口
 * 
 * @author lzpeng <liuzhanpeng@gmail.com>
 */
interface UserProviderCreatorInterface
{
    /**
     * 创建并返回提供器
     *
     * @param array $config 提供器配置项
     * @return UserProviderInterface
     */
    public function createUserProvider(array $config);
}
