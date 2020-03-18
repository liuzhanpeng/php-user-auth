<?php

namespace Lzpeng\Auth\Access;

/**
 * 权限资源对象接口
 * 
 * @author lzpeng <liuzhanpeng@gmail.com>
 */
interface ResourceInterface
{
    /**
     * 返回资源标识
     *
     * @return mixed
     */
    public function id();
}
