<?php

namespace Lzpeng\Auth;

use Lzpeng\Auth\Authenticators\NativeSessionAuthenticator;
use Lzpeng\Auth\UserProviders\NativeArrayUserProvider;

/**
 * AuthManager的外观类
 * 主要是为方便调用方使用
 * 
 * @author lzpeng <liuzhanpeng@gmail.com>
 */
class Auth
{
    /**
     * AuthManager实例
     *
     * @var AuthManager
     */
    private static $instance;

    /**
     * 初始化
     * 可重写方法实现自己的内置认证器和用户身份提供器
     *
     * @param AuthManager $authManager 认证管理器
     * @return void
     */
    protected function init($authManager)
    {
        $authManager->registerUserProviderCreator('native_array', function (array $config) {
            return new NativeArrayUserProvider($config);
        });
        $authManager->registerAuthenticatorCreator('native_session', function (array $config) {
            return new NativeSessionAuthenticator($config['session_key']);
        });
    }

    /**
     * 返回配置信息
     * 可重写方法实现自己的配置提供方式
     *
     * @return array
     */
    protected function getConfig()
    {
        return [];
    }

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    public static function __callStatic($name, $arguments)
    {
        if (is_null(static::$instance)) {
            static::$instance = new AuthManager($this->getConfig());
            $this->init(static::$instance);
        }

        return static::$instance->{$name}(...$arguments);
    }
}
