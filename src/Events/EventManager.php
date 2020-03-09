<?php

namespace Lzpeng\Auth\Events;

use Lzpeng\Auth\Contracts\EventListenerInterface;
use Lzpeng\Auth\Contracts\EventManagerInterface;
use Lzpeng\Auth\Exceptions\Exception;

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
     *          [
     *              'listener' => 'xxxx',
     *              'priority' => 100,
     *          ], ...
     *      ], ...
     * ]
     *
     * @var array
     */
    private $events = [];

    /**
     * @inheritDoc
     */
    public function attachListener(string $event, $listener, int $priority = 0)
    {
        if (!$listener instanceof EventListenerInterface && !is_callable($listener)) {
            throw new Exception('事件监听器必实现ListenerInterface或是callable对象');
        }

        if (!isset($this->events[$event])) {
            $this->events[$event] = [];
        }

        $this->events[$event][] = [
            'listener' => $listener,
            'priority' => $priority,
        ];

        // 按优化级排序
        usort($this->events[$event], function ($a, $b) {
            return ($a['priority'] <= $b['priority']) ? -1 : 1;
        });
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
            if ($item['listener'] === $listener) {
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
        foreach ($this->events[$event] as $item) {
            $listener = $item['listener'];
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

    /**
     * @inheritDoc
     */
    public function getListeners()
    {
        return $this->events;
    }
}
