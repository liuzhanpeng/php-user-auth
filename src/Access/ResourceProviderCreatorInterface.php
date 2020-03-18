<?php

namespace Lzpeng\Auth\Access;

/**
 * 权限资源提供器创建者接口
 * 
 * @author lzpeng <liuzhanpeng@gmail.com>
 */
interface ResourceProviderCreatorInterface
{
    /**
     * 创建并返回权限资源提供器
     *
     * @param array $config 配置项
     * @return ResourceProviderInterface
     */
    public function createResourceProvider(array $config);
}
