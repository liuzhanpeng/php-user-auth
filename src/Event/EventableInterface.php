<?php

namespace Lzpeng\Auth\Event;

/**
 * 事件能力接口
 * 实现该接口的认证器/访问控制器将拥有相关事件处理能力
 */
interface EventableInterface
{
    /**
     * 设置事件管理器
     *
     * @param EventManagerInterface $eventManager 事件管理器
     * @return void
     */
    public function setEventManager(EventManagerInterface $eventManager);
}
