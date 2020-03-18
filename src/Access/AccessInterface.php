<?php

namespace Lzpeng\Auth\Access;

/**
 * 权限访问接口
 * 
 * @author lzpeng <liuzhanpeng@gmail.com>
 */
interface AccessInterface
{
    /**
     * 设置权限资源提供器接口
     *
     * @param ResourceProviderInterface $provider 权限资源提供器接口
     * @return void
     */
    public function setResourceProvider(ResourceProviderInterface $provider);

    /**
     * 是否允许访问指定权限资源
     *
     * @param mixed $resourceId 资源标识
     * @return boolean
     * @throws \Lzpeng\Auth\Exception\AccessException
     */
    public function isAllowed($resourceId);
}
