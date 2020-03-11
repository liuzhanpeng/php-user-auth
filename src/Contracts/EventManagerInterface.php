<?php

namespace Lzpeng\Auth\Contracts;

/**
 * 事件管理器接口
 * 
 * @author lzpeng <liuzhanpeng@gmail.com>
 */
interface EventManagerInterface
{
    /**
     * 为指定事件附加一个监听器
     *
     * @param string $event 事件标识
     * @param EventListenerInterface|callable $listener 事件监听器
     * @return void
     */
    public function attachListener(string $event, $listener);

    /**
     * 为指定事件移除监听器
     *
     * @param string $event 事件标识
     * @param EventListenerInterface|callable|null $listener 事件监听器; 待移除的事件监听器，为null表示移除事件对应的所有监听器
     * @return void
     */
    public function detachListener(string $event, $listener = null);

    /**
     * 触发指定事件
     *
     * @param string $event 事件标识
     * @param mixed $arg 事件参数对象; 可为任意类型
     * @return void
     * @throws \Lzpeng\Auth\Exceptions\AuthException
     */
    public function trigger(string $event, $arg);
}
