<?php

namespace Lzpeng\Auth\Event;

/**
 * 事件监听器接口
 * 
 * @author lzpeng <liuzhanpeng@gmail.com>
 */
interface EventListenerInterface
{
    /**
     * 处理事件
     *
     * @param Event $event 事件对象
     * @return void
     * @throws \Lzpeng\Auth\Exception\AuthException
     */
    public function handle(Event $event);
}
