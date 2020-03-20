<?php

namespace Lzpeng\Auth\Access;


/**
 * 访问控制器创建者接口
 * 
 * @author lzpeng <liuzhanpeng@gmail.com>
 */
interface AccessorCreatorInterface
{
    /**
     * 创建并返回访问控制器
     *
     * @return AccessorInterface
     */
    public function createAccessor();
}
