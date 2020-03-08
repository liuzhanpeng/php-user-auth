<?php

namespace Lzpeng\Auth;

use Lzpeng\Auth\AuthenticatorCreatorInterface;
use Lzpeng\Auth\Exceptions\Exception;

/**
 * 用户认证管理器
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
            $name = $this->config['default'];
        }

        // 判断配置内是否存在指定认证器配置项
        if (!array_key_exists($name, $this->config['guards'])) {
            throw new Exception(sprintf('找不到[%s]认证配置项', $name));
        }

        // 如果实例已存在，直接返回
        if (isset($this->authenticators[$name])) {
            return $this->authenticators[$name];
        }

        $config = $this->config['guards'][$name];

        $userProvider = $this->createUserProvider($config['provider']);

        return $this->authenticators[$name] = $this->createAuthenticator($userProvider, $config['authenticator']);
    }

    /**
     * 创建认证器
     *
     * @param UserProviderInterface $userProvider 用户身份对象提供器
     * @param array $config 认证器配置
     * @return void
     * @throws Exceptions\Exception
     */
    private function createAuthenticator(UserProviderInterface $userProvider, array $config)
    {
        $creator = $this->authenticatorCreators[$config['dirver']];
        if ($creator instanceof AuthenticatorCreatorInterface) {
            $authenticator = $creator->createAuthenticator($userProvider, $config);
        } else {
            $authenticator = call_user_func_array($creator, [$userProvider, $config]);
        }

        if ($authenticator instanceof AuthenticatorInterface) {
            throw new Exception(sprintf('认证器[%s]必须实现AuthenticatorInterface', $config['driver']));
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
        $creator = $this->userProviderCreators[$config['dirver']];
        if ($creator instanceof UserProviderCreatorInterface) {
            $provider = $creator->createUserProvider($config);
        } else {
            $provider = call_user_func($creator, $config);
        }

        if ($provider instanceof UserProviderInterface) {
            throw new Exception(sprintf('用户身份对象提供器[%s]必须实现UserProviderInterface', $config['driver']));
        }

        return $provider;
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
