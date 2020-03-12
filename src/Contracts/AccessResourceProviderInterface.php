<?php

namespace Lzpeng\Auth\Contracts;

/**
 * 权限资源提供器接口
 * 
 * @author lzpeng <liuzhanpeng@gmail.com>
 */
interface AccessResourceProviderInterface
{
    /**
     * 返回资源列表
     *
     * @return array
     */
    public function getAccessResources();
}
