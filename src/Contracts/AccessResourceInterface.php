<?php

namespace Lzpeng\Auth\Contracts;

/**
 * 权限资源对象接口
 * 
 * @author lzpeng <liuzhanpeng@gmail.com>
 */
interface AccessResourceInterface
{
    /**
     * 返回资源标识
     *
     * @return string
     */
    public function id();
}
