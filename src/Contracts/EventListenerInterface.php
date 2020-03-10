<?php

namespace Lzpeng\Auth\Contracts;

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
     * @param mixed $arg 事件对象; 可为任意类型
     * @return void
     * @throws \Lzpeng\Auth\Exceptions\Exception
     */
    public function handle($arg);
}
