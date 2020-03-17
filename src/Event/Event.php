<?php

namespace Lzpeng\Auth\Event;

/**
 * 事件对象
 * 在认证事件处理过程中充当载体, 传递事件中信息
 * 
 * @author lzpeng <liuzhanpeng@gmail.com>
 */
class Event implements \ArrayAccess
{
    /**
     * 数据
     *
     * @var array
     */
    private $data;

    /**
     * 是否已停止
     *
     * @var boolean
     */
    private $isStopped = false;

    /**
     * 构造函数
     *
     * @param array $data 数据
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * 事件是否已标识为停止传播
     *
     * @return boolean
     */
    public function isStopped()
    {
        return $this->isStopped;
    }

    /**
     * 设置事件停止传播标识
     *
     * @return void
     */
    public function stop()
    {
        $this->isStopped = true;
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset)
    {
        return $this->data[$offset];
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value)
    {
        $this->data[$offset] = $value;
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

    public function __get($key)
    {
        return $this->data[$key];
    }

    public function __set($key, $value)
    {
        $this->data[$key] = $value;
    }

    public function __isset($key)
    {
        return isset($this->data[$key]);
    }

    public function __unset($key)
    {
        unset($this->data[$key]);
    }
}
