<?php

namespace Lzpeng\Auth\Access;

use Lzpeng\Auth\Event\EventManagerInterface;

/**
 * 具事件功能的权限访问接口
 * 
 * @author lzpeng <liuzhanpeng@gmail.com>
 */
interface EventableAccessorInterface extends AccessorInterface
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
     * 设置事件管理器
     *
     * @param EventManagerInterface $eventManager 事件管理器
     * @return void
     */
    public function setEventManager(EventManagerInterface $eventManager);
}
