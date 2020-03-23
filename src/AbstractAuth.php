<?php

namespace Lzpeng\Auth;

use Lzpeng\Auth\Authenticators\NativeSessionAuthenticator;
use Lzpeng\Auth\ResourceProviders\NativeArrayResourceProvider;
use Lzpeng\Auth\UserProviders\NativeArrayUserProvider;

/**
 * AuthManager的外观类
 * 主要是为方便调用方使用
 * 
 * @author lzpeng <liuzhanpeng@gmail.com>
 */
abstract class AbstractAuth
{
    /**
     * AuthManager实例
     *
     * @var AuthManager
     */
    protected static $instance;

    /**
     * 初始化
     * 可重写方法实现自己的内置认证器和用户身份提供器
     *
     * @param AuthManager $authManager 认证管理器
     * @return void
     */
    static protected function init($authManager)
    {
        $authManager->registerUserProviderCreator('native_array', function (array $config) {
            return new NativeArrayUserProvider($config);
        });
        $authManager->registerAuthenticatorCreator('native_session', function (array $config) {
            return new NativeSessionAuthenticator($config['session_key']);
        });
        $authManager->registerResourceProvider('native_array', function ($config) {
            return new NativeArrayResourceProvider($config);
        });
    }

    /**
     * 返回配置信息
     * 可重写方法实现自己的配置提供方式
     *
     * @return array
     */
    abstract static protected function getConfig();

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    public static function __callStatic($name, $arguments)
    {
        if (is_null(static::$instance)) {
            static::$instance = new AuthManager(static::getConfig());
            $this->init(static::$instance);
        }

        return static::$instance->{$name}(...$arguments);
    }
}
