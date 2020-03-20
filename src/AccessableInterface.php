<?php

namespace Lzpeng\Auth;

use Lzpeng\Auth\Access\AccessorInterface;

/**
 * 访问控制接口
 * 实现该接口的认证器将具有访问控制的能力
 * 
 * @author lzpeng <liuzhanpeng@gmail.com>
 */
interface AccessableInterface
{
    /**
     * 设置访问控制器
     *
     * @param AccessorInterface $accessor 访问控制器
     * @return void
     */
    public function setAccessor(AccessorInterface $accessor);

    /**
     * 判断当前用户是否允许访问指定权限资源
     *
     * @param string $resourceId 资源标识
     * @return boolean
     * @throws \Lzpeng\Auth\Exception\AccessException
     */
    public function isAllowed(string $resourceId);
}
