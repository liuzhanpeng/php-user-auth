<?php

namespace Lzpeng\Auth\ResourceProviders;

use Lzpeng\Auth\Access\GenericResource;
use Lzpeng\Auth\Access\ResourceProviderInterface;

/**
 * 基于原生数组的权限资源提供器
 * 
 * @author lzpeng <liuzhanpeng@gmail.com>
 */
class NativeArrayResourceProvider implements ResourceProviderInterface
{
    /**
     * 权限配置
     *
     * @var array
     */
    private $config;

    /**
     * 构造函数
     * 
     * 配置例子:
     * 
     * [
     *      [
     *          'id' => 1,
     *          'name' => 'xxx',
     *          ....,
     *          'resources' => [
     *              'resource1', 'resource2', ... 
     *          ]
     *      ], ...
     * ]
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @inheritDoc
     */
    public function getResources($user)
    {
        $resources = [];
        foreach ($this->config as $item) {
            if ($item['id'] == $user->id()) {
                foreach ($item['resources'] as $resource) {
                    $resources[] = new GenericResource($resource);
                }
            }
        }

        return $resources;
    }
}
