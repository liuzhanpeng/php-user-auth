<?php

namespace Lzpeng\Auth\Authenticators;

use Lzpeng\Auth\Contracts\UserInterface;

/**
 * 基于原生session的认证器
 * 
 * @author lzpeng <liuzhanpeng@gmail.com>
 */
class NativeSessionAuthenticator extends AbstractAuthenticator
{
    /**
     * 会话key
     *
     * @var string
     */
    private $sessionKey;

    public function __construct(string $sessionKey)
    {
        $this->sessionKey = $sessionKey;
    }

    /**
     * @inheritDoc
     */
    protected function storeUser(UserInterface $user)
    {
        $_SESSION[$this->sessionKey] = $user;

        return;
    }

    /**
     * @inheritDoc
     */
    public function loadUser()
    {
        return $_SESSION[$this->sessionKey];
    }

    /**
     * @inheritDoc
     */
    public function clearUser()
    {
        unset($_SESSION[$this->sessionKey]);
    }
}
