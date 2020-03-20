<?php

namespace Lzpeng\Auth\Access;

/**
 * 访问控制器创建者
 * 
 * @author lzpeng <liuzhanpeng@gmail.com>
 */
class AccessorCreator implements AccessorCreatorInterface
{
    /**
     * @inheritDoc
     */
    public function createAccessor()
    {
        return new Accessor();
    }
}
