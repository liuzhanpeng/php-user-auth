<?php

namespace Lzpeng\Auth\Authenticators;

use Lzpeng\Auth\Contracts\AuthenticatorInterface;
use Lzpeng\Auth\Contracts\AuthEventInterface;
use Lzpeng\Auth\Contracts\UserInterface;
use Lzpeng\Auth\Exceptions\AuthException;
use Lzpeng\Auth\Exceptions\InvalidCredentialException;

/**
 * 抽象认证器
 * 
 * @author lzpeng <liuzhanpeng@gmail.com>
 */
abstract class AbstractAuthenticator implements AuthenticatorInterface, AuthEventInterface
{
    /**
     * 用户身份对象
     *
     * @var UserInterface
     */
    protected $user;

    /**
     * 事件管理器
     *
     * @var \Lzpeng\Auth\Contracts\EventManagerInterface
     */
    private $eventManager;

    /**
     * 用户身份对象提供器
     *
     * @var \Lzpeng\Auth\Contracts\UserProviderInterface
     */
    private $userProvider;

    /**
     * @inheritDoc
     */
    public function setEventManager($eventManager)
    {
        $this->eventManager = $eventManager;
    }

    /**
     * @inheritDoc
     */
    public function getEventManager()
    {
        return $this->eventManager;
    }

    /**
     * 设置用户身份对象提供器
     *
     * @param \Lzpeng\Auth\Contracts\UserProviderInterface $userProvider 用户身份对象提供器
     * @return void
     */
    public function setUserProvider($userProvider)
    {
        $this->userProvider = $userProvider;
    }

    /**
     * @inheritDoc
     */
    public function getUserProvider()
    {
        return $this->userProvider;
    }

    /**
     * @inheritDoc
     */
    public function login(array $credentials)
    {
        try {
            $this->getEventManager()->trigger(self::EVENT_LOGIN_BEFORE, [
                'credentials' => $credentials,
            ]);

            $user = $this->getUserProvider()->findByCredentials($credentials);
            if (is_null($user)) {
                throw new InvalidCredentialException('无效的用户凭证');
            }

            $this->getUserProvider()->validateCredentials($user, $credentials);

            $result = $this->storeUser($user);
            $this->user = $user;

            $this->getEventManager()->trigger(self::EVENT_LOGIN_SUCCESS, [
                'credentials' => $credentials,
                'user' => $user,
                'result' => $result,
            ]);

            return $result;
        } catch (AuthException $ex) {
            $this->getEventManager()->trigger(self::EVENT_LOGIN_FAILURE, [
                'credentials' => $credentials,
                'exception' => $ex,
            ]);

            throw $ex;
        }
    }

    /**
     * @inheritDoc
     */
    public function isLogined()
    {
        return !is_null($this->user());
    }

    /**
     * @inheritDoc
     */
    public function id()
    {
        $user = $this->loadUser();
        if (is_null($user)) {
            return null;
        }

        return $user->id();
    }

    /**
     * @inheritDoc
     */
    public function user()
    {
        return $this->loadUser();
    }

    /**
     * @inheritDoc
     */
    public function setUser(UserInterface $user)
    {
        try {
            $result = $this->storeUser($user);
            $this->user = $user;

            $this->getEventManager()->trigger(self::EVENT_LOGIN_SUCCESS, [
                'user' => $user,
                'result' => $result,
            ]);

            return $result;
        } catch (AuthException $ex) {
            $this->getEventManager()->trigger(self::EVENT_LOGIN_FAILURE, [
                'exception' => $ex,
            ]);

            throw $ex;
        }
    }

    /**
     * @inheritDoc
     *
     * @return void
     */
    public function logout()
    {
        $this->getEventManager()->trigger(self::EVENT_LOGOUT_BEFORE, [
            'user' => $this->user(),
        ]);

        $this->clearUser();
        $this->user = null;

        $this->getEventManager()->trigger(self::EVENT_LOGUT_AFTER, null);
    }

    /**
     * 持久化用户信息
     *
     * @param UserInterface $user 用户身份对象
     * @return void
     */
    abstract protected function storeUser(UserInterface $user);

    /**
     * 加载持久化的用户信息
     *
     * @return UserInterface|null
     */
    abstract protected function loadUser();

    /**
     * 清除持久化的用户信息
     *
     * @return void
     */
    abstract protected function clearUser();
}
