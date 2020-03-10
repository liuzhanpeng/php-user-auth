<?php

namespace Lzpeng\Auth\Events;

use Lzpeng\Auth\Contracts\EventManagerCreatorInterface;

/**
 * 内部默认的事件管理器创建者
 * 
 * @author lzpeng <liuzhanpeng@gmail.com>
 */
class EventManagerCreator extends EventManagerCreatorInterface
{
    /**
     * @inheritDoc
     */
    public function createEventManager()
    {
        return new EventManager();
    }
}
