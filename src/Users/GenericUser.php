<?php

namespace Lzpeng\Auth\Users;

use Lzpeng\Auth\Exception\Exception;
use Lzpeng\Auth\UserInterface;

/**
 * 通用用户类
 * 
 * @author lzpeng <liuzhanpeng@gmail.com>
 */
class GenericUser implements UserInterface, \ArrayAccess
{
    /**
     * 用户数据
     *
     * @var array
     */
    private $data;

    /**
     * 用户标识对应key
     *
     * @var string
     */
    private $idKey;

    /**
     * 构造函数
     *
     * @param array $data 用户数据
     * @param string $idKey 用户标识对应key
     */
    public function __construct(array $data, string $idKey = 'id')
    {
        $this->data = $data;
        $this->idKey = $idKey;
    }

    /**
     * @inheritDoc
     */
    public function id()
    {
        if (!isset($this->data[$this->idKey])) {
            throw new Exception('无效idkey');
        }

        return $this->data[$this->idKey];
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
