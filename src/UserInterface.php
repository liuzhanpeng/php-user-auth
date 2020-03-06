<?php

namespace Lzpeng\Auth;

/**
 * 用户身份对象接口
 * 
 * @author lzpeng <liuzhanpeng@gmail.com>
 */
interface UserInterface
{
    /**
     * 返回用户唯一身份标识
     *
     * @return mixed
     */
    public function getId();
}
