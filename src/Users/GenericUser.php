<?php

namespace Lzpeng\Auth\Users;

use Lzpeng\Auth\Contracts\UserInterface;

/**
 * 通用用户类
 * 
 * @author lzpeng <liuzhanpeng@gmail.com>
 */
class GenericUser implements UserInterface
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
        return $this->data[$this->idKey];
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
}
}
