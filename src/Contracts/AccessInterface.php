<?php

namespace Lzpeng\Auth\Contracts;

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
     * @param AccessResourceProviderInterface $provider 权限资源提供器接口
     * @return void
     */
    public function setAccessSourceProvider(AccessResourceProviderInterface $provider);

    /**
     * 返回权限资源提供器接口
     *
     * @return AccessResourceProviderInterface
     */
    public function getAccessSourceProvider();

    /**
     * 是否允许访问指定权限资源
     *
     * @param mixed $resourceId 资源标识
     * @return boolean
     * @throws \Lzpeng\Auth\Exceptions\AccessException
     */
    public function isAllowed($resourceId);
}
