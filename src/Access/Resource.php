<?php

namespace Lzpeng\Auth\Access;

/**
 * 权限资源对象
 * 
 * @author lzpeng <liuzhanpeng@gmail.com>
 */
class Resource implements AccessResourceInterface, \ArrayAccess
{
    /**
     * 权限资源标识
     *
     * @var mixed
     */
    public $id;

    /**
     * 数据
     *
     * @var array
     */
    public $data;

    public function __construct($id, array $data = [])
    {
        $this->id = $id;
        $this->data = $data;
    }

    /**
     * @inheritDoc
     */
    public function id()
    {
        return $this->id;
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
