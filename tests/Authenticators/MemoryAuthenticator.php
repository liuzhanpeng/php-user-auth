<?php

namespace Lzpeng\Tests\Authenticators;

use Lzpeng\Auth\Authenticators\AbstractAuthenticator;

use Lzpeng\Auth\Contracts\UserInterface;

class MemoryAuthenticator extends AbstractAuthenticator
{
    /**
     * 会话数据
     *
     * @var array
     */
    private $sessions = [];

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
        $this->sessions[$this->sessionKey] = $user;

        return;
    }

    /**
     * @inheritDoc
     */
    public function loadUser()
    {
        if (!isset($this->sessions[$this->sessionKey])) {
            return null;
        }

        return $this->sessions[$this->sessionKey];
    }

    /**
     * @inheritDoc
     */
    public function clearUser()
    {
        unset($this->sessions[$this->sessionKey]);
    }
}
