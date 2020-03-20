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
     * 访问权限资源前事件
     */
    const EVENT_ACCESS_BEFORE = 'access_before';

    /**
     * 访问权限资源后事件
     */
    const EVENT_ACCESS_AFTER = 'access_after';

    /**
     * 设置访问控制器
     *
     * @param AccessorInterface $accessor 访问控制器
     * @return void
     */
    public function setAccessor(AccessorInterface $accessor);
}
