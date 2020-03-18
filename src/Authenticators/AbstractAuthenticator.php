<?php

namespace Lzpeng\Auth\Authenticators;

use Lzpeng\Auth\Access\AccessInterface;
use Lzpeng\Auth\Access\ResourceProviderInterface;
use Lzpeng\Auth\AuthenticatorInterface;
use Lzpeng\Auth\UserProviderInterface;
use Lzpeng\Auth\UserInterface;
use Lzpeng\Auth\AuthEventInterface;
use Lzpeng\Auth\Event\Event;
use Lzpeng\Auth\Exception\Exception;
use Lzpeng\Auth\Exception\AccessException;
use Lzpeng\Auth\Exception\AuthException;
use Lzpeng\Auth\Exception\InvalidCredentialException;

/**
 * 抽象认证器
 * 
 * @author lzpeng <liuzhanpeng@gmail.com>
 */
abstract class AbstractAuthenticator implements AuthenticatorInterface, AuthEventInterface, AccessInterface
{
    /**
     * 事件管理器
     *
     * @var \Lzpeng\Auth\Event\EventManagerInterface
     */
    private $eventManager;

    /**
     * 用户身份对象提供器
     *
     * @var \Lzpeng\Auth\UserProviderInterface
     */
    private $userProvider;

    /**
     * 权限资源提供器
     *
     * @var \Lzpeng\Auth\Access\ResourceProviderInterface
     */
    private $resourceProvider;

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
     * @param \Lzpeng\Auth\UserProviderInterface $userProvider 用户身份对象提供器
     * @return void
     */
    public function setUserProvider(UserProviderInterface $userProvider)
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
    public function setResourceProvider(ResourceProviderInterface $resourceProvider)
    {
        $this->resourceProvider = $resourceProvider;
    }

    /**
     * @inheritDoc
     */
    public function getResourceProvider()
    {
        return $this->resourceProvider;
    }

    /**
     * @inheritDoc
     */
    public function login(array $credentials)
    {
        try {
            $this->getEventManager()->dispatch(self::EVENT_LOGIN_BEFORE, new Event([
                'credentials' => $credentials,
            ]));

            $user = $this->getUserProvider()->findByCredentials($credentials);
            if (is_null($user)) {
                throw new InvalidCredentialException('无效的用户凭证');
            }

            $this->getUserProvider()->validateCredentials($user, $credentials);

            $result = $this->storeUser($user);

            $this->getEventManager()->dispatch(self::EVENT_LOGIN_SUCCESS, new Event([
                'credentials' => $credentials,
                'user' => $user,
                'result' => $result,
            ]));

            return $result;
        } catch (AuthException $ex) {
            $this->getEventManager()->dispatch(self::EVENT_LOGIN_FAILURE, new Event([
                'credentials' => $credentials,
                'exception' => $ex,
            ]));

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
        $user = $this->user();
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

            $this->getEventManager()->dispatch(self::EVENT_LOGIN_SUCCESS, new Event([
                'user' => $user,
                'result' => $result,
            ]));

            return $result;
        } catch (AuthException $ex) {
            $this->getEventManager()->dispatch(self::EVENT_LOGIN_FAILURE, new Event([
                'exception' => $ex,
            ]));

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
        $this->getEventManager()->dispatch(self::EVENT_LOGOUT_BEFORE, new Event([
            'user' => $this->user(),
        ]));

        $this->clearUser();

        $this->getEventManager()->dispatch(self::EVENT_LOGUT_AFTER, new Event());
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

    /**
     * @inheritDoc
     */
    public function isAllowed($resourceId)
    {
        if (!$this->isLogined()) {
            throw new AccessException('用户还未登录认证');
        }

        $this->getEventManager()->dispatch(self::EVENT_ACCESS_BEFORE, new Event([
            'resourceId' => $resourceId,
            'user' => $this->user(),
        ]));

        $provider = $this->getResourceProvider();
        if (is_null($provider)) {
            throw new Exception('找不到权限资源提供器');
        }

        $result = false;
        foreach ($provider->getResources($this->user()) as $resource) {
            if ($resource->id() === $resourceId) {
                $result = true;
                break;
            }
        }

        $this->getEventManager()->dispatch(self::EVENT_ACCESS_AFTER, new Event([
            'resourceId' => $resourceId,
            'user' => $this->user(),
            'isAllowed' => $result,
        ]));

        return $result;
    }
}
