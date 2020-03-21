<?php

namespace Lzpeng\Auth\Authenticators;

use Lzpeng\Auth\Access\AccessorInterface;
use Lzpeng\Auth\AccessableInterface;
use Lzpeng\Auth\UserProviderInterface;
use Lzpeng\Auth\UserInterface;
use Lzpeng\Auth\Event\Event;
use Lzpeng\Auth\EventableAuthenticatorInterface;
use Lzpeng\Auth\Exception\Exception;
use Lzpeng\Auth\Exception\AuthException;
use Lzpeng\Auth\Exception\InvalidCredentialException;

/**
 * 抽象认证器
 * 
 * @author lzpeng <liuzhanpeng@gmail.com>
 */
abstract class AbstractAuthenticator implements EventableAuthenticatorInterface, AccessableInterface
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
     * 访问控制器
     *
     * @var \Lzpeng\Auth\Access\AccessorInterface
     */
    private $accessor;

    /**
     * @inheritDoc
     */
    public function setEventManager($eventManager)
    {
        $this->eventManager = $eventManager;
    }

    /**
     * 返回事件管理器
     *
     * @return EventManagerInterface
     * @throws Exception
     */
    protected function getEventManager()
    {
        if (is_null($this->eventManager)) {
            throw new Exception('认证器未设置事件管理器');
        }

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
     * 返回用户身份对象提供器
     *
     * @return UserProviderInterface
     * @throws Exception
     */
    public function getUserProvider()
    {
        if (is_null($this->userProvider)) {
            throw new Exception('认证器未设置用户身份对象提供器');
        }

        return $this->userProvider;
    }

    /**
     * @inheritDoc
     */
    public function setAccessor(AccessorInterface $accessor)
    {
        $this->accessor = $accessor;
    }

    /**
     * 获取访问控制器
     *
     * @return AccessorInterface
     */
    public function getAccessor()
    {
        if (is_null($this->accessor)) {
            throw new Exception('认证器未设置访问控制器');
        }

        return $this->accessor;
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
     * 判断当前用户是否允许访问指定权限资源
     *
     * @param string $resourceId 资源标识
     * @return boolean
     */
    public function isAllowed(string $resourceId)
    {
        if (!$this->isLogined()) {
            return false;
        }

        return $this->getAccessor()->isAllowed($this->user(), $resourceId);
    }

    /**
     * 持久化用户信息
     *
     * @param UserInterface $user 用户身份对象
     * @return mixed
     * @throws AuthException
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
     * @throws AuthException
     */
    abstract protected function clearUser();
}
