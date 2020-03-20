<?php

namespace Lzpeng\Auth\Access;

use Lzpeng\Auth\UserInterface;

/**
 * 权限访问接口
 * 
 * @author lzpeng <liuzhanpeng@gmail.com>
 */
interface AccessorInterface
{
    /**
     * 设置权限资源提供器接口
     *
     * @param ResourceProviderInterface $provider 权限资源提供器接口
     * @return void
     */
    public function setResourceProvider(ResourceProviderInterface $provider);

    /**
     * 判断用户是否允许访问指定权限资源
     *
     * @param UserInterface $user 用户身份对象
     * @param string $resourceId 资源标识
     * @return boolean
     * @throws \Lzpeng\Auth\Exception\AccessException
     */
    public function isAllowed(UserInterface $user, string $resourceId);
}
