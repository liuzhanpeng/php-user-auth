<?php

namespace Lzpeng\Auth\Event;

/**
 * 内部默认的事件管理器创建者
 * 
 * @author lzpeng <liuzhanpeng@gmail.com>
 */
class EventManagerCreator implements EventManagerCreatorInterface
{
    /**
     * @inheritDoc
     */
    public function createEventManager()
    {
        return new EventManager();
    }
}
