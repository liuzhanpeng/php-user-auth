<?php

namespace Lzpeng\Auth;

use Lzpeng\Auth\Contracts\AuthenticatorCreatorInterface;
use Lzpeng\Auth\Contracts\UserProviderCreatorInterface;
use Lzpeng\Auth\Contracts\AuthenticatorInterface;
use Lzpeng\Auth\Contracts\AuthEventInterface;
use Lzpeng\Auth\Contracts\EventListenerInterface;
use Lzpeng\Auth\Contracts\EventManagerCreatorInterface;
use Lzpeng\Auth\Contracts\EventManagerInterface;
use Lzpeng\Auth\Contracts\UserProviderInterface;
use Lzpeng\Auth\Events\EventManagerCreator;
use Lzpeng\Auth\Exceptions\ConfigException;
use Lzpeng\Auth\Exceptions\Exception;

/**
 * 用户认证管理器
 * 对内，管理并组合认证器和用户身份提供器
 * 对外，创建并返回认证器实例
 * 
 * @author lzpeng <liuzhanpeng@gmail.com>
 */
class AuthManager
{
    /**
     * 配置
     *
     * @var array
     */
    private $config;

    /**
     * 已创建的认证器实例列表
     *
     * @var array
     */
    private $authenticators = [];

    /**
     * 已注册的自定义认证器创建者列表
     * 认证器创建者类型可以是AuthenticatorCreatorInterface、callable; 如果是callable，必须返回实现了AuthenticatorInterface的实例对象
     *
     * @var array
     */
    private $authenticatorCreators = [];

    /**
     * 已注册的自定义用户身份对象提供器创建者列表
     * 用户身份对象提供器创建者可以是UserProviderCreatorInterface、callable; 如果是callable，必须返回实现了UserProviderInterface的实例对象
     *
     * @var array
     */
    private $userProviderCreators = [];

    /**
     * 事件管理器创建者
     *
     * @var EventManagerCreatorInterface
     */
    private $eventManagerCreator;

    /**
     * 有效的认证事件
     *
     * @var array
     */
    private $availableEvents = [
        AuthEventInterface::EVENT_LOGIN_BEFORE,
        AuthEventInterface::EVENT_LOGIN_FAILURE,
        AuthEventInterface::EVENT_LOGIN_SUCCESS,
        AuthEventInterface::EVENT_LOGOUT_BEFORE,
        AuthEventInterface::EVENT_LOGUT_AFTER
    ];

    /**
     * 构造函数
     * 
     * @param array $config 配置
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * 创建并返回认证器
     * 内部使用数组保存认证器列表，同一键名只创建一个实例
     *
     * @param string $name 配置键名
     * @return AuthenticatorInterface
     * @throws Exceptions\Exception
     */
    public function create($name = null)
    {
        if (is_null($name)) {
            if (!isset($this->config['default'])) {
                throw new ConfigException('找不到默认认证配置项');
            }

            $name = $this->config['default'];
        }

        // 判断配置内是否存在指定认证器配置项
        if (!array_key_exists($name, $this->config['authenticators'])) {
            throw new ConfigException(sprintf('找不到认证配置项[%s]', $name));
        }

        // 如果实例已存在，直接返回
        if (isset($this->authenticators[$name])) {
            return $this->authenticators[$name];
        }

        $config = $this->config['authenticators'][$name];

        $userProvider = $this->createUserProvider($config['provider']);

        return $this->authenticators[$name] = $this->createAuthenticator($userProvider, $config);
    }

    /**
     * 注册认证器创建者
     *
     * @param string $driver 认证器的驱动名称; 用于区别不同的认证器
     * @param AuthenticatorCreatorInterface|callable $creator 创建者
     * @return void
     * @throws Exceptions\Exception
     */
    public function registerAuthenticatorCreator(string $driver, $creator)
    {
        if (!$creator instanceof AuthenticatorCreatorInterface && !is_callable($creator)) {
            throw new Exception('认证器创建者必实现AuthenticatorCreatorInterface或是返回实现了AuthenticatorInterface的实例的callable');
        }

        $this->authenticatorCreators[$driver] = $creator;
    }

    /**
     * 注册用户身份对象提供器创建者
     *
     * @param string $driver 认证器的驱动名称; 用于区别不同的提供器
     * @param UserProviderCreatorInterface|callable $creator 创建者
     * @return void
     * @throws Exceptions\Exception
     */
    public function registerUserProviderCreator(string $driver, $creator)
    {
        if (!$creator instanceof UserProviderCreatorInterface && !is_callable($creator)) {
            throw new Exception('用户身份对象器创建者必实现UserProviderCreatorInterface或是返回实现了UserProviderInterface的实例的callable');
        }

        $this->userProviderCreators[$driver] = $creator;
    }

    /**
     * 设置事件管理器创建者
     *
     * @param EventManagerCreatorInterface $creator 事件管理器创建者
     * @return void
     */
    public function setEventManagerCreator(EventManagerCreatorInterface $creator)
    {
        $this->eventManagerCreator = $creator;
    }

    /**
     * 创建认证器
     *
     * @param UserProviderInterface $userProvider 用户身份对象提供器
     * @param array $config 认证器配置
     * @return AuthenticatorInterface
     * @throws Exceptions\Exception
     */
    private function createAuthenticator(UserProviderInterface $userProvider, array $config)
    {
        if (!isset($this->authenticatorCreators[$config['driver']])) {
            throw new ConfigException(sprintf('找不到认证器驱动[%s]', $config['driver']));
        }

        $creator = $this->authenticatorCreators[$config['driver']];
        if ($creator instanceof AuthenticatorCreatorInterface) {
            $authenticator = $creator->createAuthenticator($config['params']);
        } else {
            $authenticator = call_user_func_array($creator, [$config['params']]);
        }
        $authenticator->setUserProvider($userProvider);

        if (!$authenticator instanceof AuthenticatorInterface) {
            throw new Exception(sprintf('认证器[%s]必须实现AuthenticatorInterface', $config['driver']));
        }

        if ($authenticator instanceof AuthEventInterface) {
            if (is_null($this->eventManagerCreator)) {
                // 如果没设置自定义的事件管理器创建者，用内部默认的
                $this->eventManagerCreator = new EventManagerCreator();
            }

            // 注册事件
            $eventManager = $this->eventManagerCreator->createEventManager();
            if (isset($config['events'])) {
                $this->registerEvents($eventManager, $config['events']);
            }

            // 每个authenticator独立的事件管理器
            $authenticator->setEventManager($eventManager);
        }

        return $authenticator;
    }

    /**
     * 创建用户身份对象提供器
     *
     * @param array $config 提供器配置项
     * @return UserProviderInterface
     * @throws Exceptions\Exception
     */
    private function createUserProvider(array $config)
    {
        if (!isset($this->userProviderCreators[$config['driver']])) {
            throw new ConfigException(sprintf('找不到用户身份提供器驱动[%s]', $config['driver']));
        }

        $creator = $this->userProviderCreators[$config['driver']];
        if ($creator instanceof UserProviderCreatorInterface) {
            $provider = $creator->createUserProvider($config['params']);
        } else {
            $provider = call_user_func($creator, $config['params']);
        }

        if (!$provider instanceof UserProviderInterface) {
            throw new Exception(sprintf('用户身份对象提供器[%s]必须实现UserProviderInterface', $config['driver']));
        }

        return $provider;
    }

    /**
     * 注册事件
     *
     * @param EventManagerInterface $eventManager 事件管理器
     * @param array $config 配置
     * @return void
     */
    private function registerEvents(EventManagerInterface $eventManager, array $config)
    {
        foreach ($config as $event => $listeners) {
            if (!in_array($event, $this->availableEvents)) {
                throw new ConfigException(sprintf('不支持的认证事件[%s]', $event));
            }

            foreach ($listeners as $listener) {
                if (is_callable($listener)) {
                    $eventManager->attachListener($event, $listener);
                } elseif (is_string($listener)) {
                    try {
                        $instance = new $listener();
                    } catch (\Error $ex) {
                        throw new Exception(sprintf('无效的事件监听器[%s]', $listener));
                    }

                    $eventManager->attachListener($event, $instance);
                } else {
                    throw new Exception(sprintf('事件监听器[%s]必须实现\Lzpeng\Auth\Contracts\EventListenerInterface接口或是callable对象', $listener));
                }
            }
        }
    }

    /**
     * 调用默认authenticator的实例方法
     * 
     * @param string $method 方法名称
     * @param array $arguments 方法参数
     */
    public function __call($method, $arguments)
    {
        return $this->create()->{$method}(...$arguments);
    }
}
