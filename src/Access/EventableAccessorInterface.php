<?php

namespace Lzpeng\Auth\Access;

use Lzpeng\Auth\Event\EventableInterface;

/**
 * 具事件功能的权限访问接口
 * 
 * @author lzpeng <liuzhanpeng@gmail.com>
 */
interface EventableAccessorInterface extends AccessorInterface, EventableInterface
{
    /**
     * 访问权限资源前事件
     */
    const EVENT_ACCESS_BEFORE = 'access_before';

    /**
     * 访问权限资源后事件
     */
    const EVENT_ACCESS_AFTER = 'access_after';
}
