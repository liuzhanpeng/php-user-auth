<?php

namespace Lzpeng\Auth\Events;

use Lzpeng\Auth\Contracts\EventListenerInterface;
use Lzpeng\Auth\Contracts\EventManagerInterface;
use Lzpeng\Auth\Exceptions\EventException;

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
    public function attachListener(string $event, $listener)
    {
        if (!$listener instanceof EventListenerInterface && !is_callable($listener)) {
            throw new EventException('事件监听器必实现ListenerInterface或是callable对象');
        }

        if (!isset($this->events[$event])) {
            $this->events[$event] = [];
        }

        $this->events[$event][] = $listener;
    }

    /**
     * @inheritDoc
     */
    public function detachListener(string $event, $listener = null)
    {
        if (is_null($listener)) {
            $this->events[$event] = [];
            return;
        }

        foreach ($this->events[$event] as $key => $item) {
            if ($item === $listener) {
                unset($this->events[$event][$key]);
                break;
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function trigger(string $event, $arg)
    {
        if (!isset($this->events[$event])) {
            return;
        }

        foreach ($this->events[$event] as $listener) {
            if ($listener instanceof EventListenerInterface) {
                $result = $listener->handle($arg);
            } else {
                $result = call_user_func($listener, $arg);
            }

            // 监听器返回false, 中断事件分发
            if ($result === false) {
                break;
            }
        }
    }
}
