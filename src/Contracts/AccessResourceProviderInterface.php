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
     * @param UserInterface $user 用户身份对象
     * @return array
     */
    public function getAccessResources($user);
}
