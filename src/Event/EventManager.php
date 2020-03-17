<?php

namespace Lzpeng\Auth\Event;

use Lzpeng\Auth\Exception\EventException;

/**
 * 内部默认的事件管理器
 * 
 * @author lzpeng <liuzhanpeng@gmail.com>
 */
class EventManager implements EventManagerInterface
{
    /**
     * 事件列表
     * 保存已注册的事件及其对应的监听器
     * 
     * [
     *      'login_before' => [
     *          $listener1, 
     *          $listener2,
     *          ... 
     *      ], ...
     * ]
     *
     * @var array
     */
    private $events = [];

    /**
     * @inheritDoc
     */
    public function addListener(string $name, $listener)
    {
        if (!$listener instanceof EventListenerInterface && !is_callable($listener)) {
            throw new EventException('事件监听器必实现ListenerInterface或是callable对象');
        }

        if (!isset($this->events[$name])) {
            $this->events[$name] = [];
        }

        $this->events[$name][] = $listener;
    }

    /**
     * @inheritDoc
     */
    public function removeListener(string $name, $listener = null)
    {
        if (is_null($listener)) {
            $this->events[$name] = [];
            return;
        }

        foreach ($this->events[$name] as $key => $item) {
            if ($item === $listener) {
                unset($this->events[$name][$key]);
                break;
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function dispatch(string $name, Event $event)
    {
        if (!isset($this->events[$name])) {
            return;
        }

        foreach ($this->events[$name] as $listener) {
            if ($event->isStopped()) {
                // 事件已标识停止分发，直接返回
                break;
            }

            if ($listener instanceof EventListenerInterface) {
                $listener->handle($event);
            } else {
                call_user_func($listener, $event);
            }
        }
    }
}
