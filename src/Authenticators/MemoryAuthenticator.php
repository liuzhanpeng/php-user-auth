<?php

namespace Lzpeng\Auth\Authenticators;

use Lzpeng\Auth\Authenticators\AbstractAuthenticator;
use Lzpeng\Auth\Authenticators\Session\SessionInterface;
use Lzpeng\Auth\UserInterface;

/**
 * 基于内存的认证器
 * 认证只会在一次请求中有效
 * 
 * @author lzpeng <liuzhanpeng@gmail.com>
 */
class MemoryAuthenticator extends AbstractAuthenticator
{
    /**
     * 用户身份对象
     *
     * @var \Lzpeng\Auth\UserInterface
     */
    protected $user;

    /**
     * @inheritDoc
     */
    protected function storeUser(UserInterface $user)
    {
        $this->user = $user;
    }

    /**
     * @inheritDoc
     */
    public function loadUser()
    {
        return $this->user;
    }

    /**
     * @inheritDoc
     */
    public function clearUser()
    {
        $this->user = null;
    }
}
