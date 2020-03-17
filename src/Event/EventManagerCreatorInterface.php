<?php

namespace Lzpeng\Auth\Event;

/**
 * 事件管理器创建者接口
 * 
 * @author lzpeng <liuzhanpeng@gmail.com>
 */
interface EventManagerCreatorInterface
{
    /**
     * 创建并返回事件管理器
     *
     * @return EventManagerInterface
     */
    public function createEventManager();
}
