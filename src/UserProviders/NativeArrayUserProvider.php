<?php

namespace Lzpeng\Auth\UserProviders;

use Lzpeng\Auth\Contracts\UserProviderInterface;
use Lzpeng\Auth\Contracts\UserInterface;
use Lzpeng\Auth\Exceptions\InvalidCredentialException;
use Lzpeng\Auth\Users\GenericUser;

/**
 * 基于原生数组的简单的用户身份对象提供器
 * 使用账号/密码的认证方式; 主要是一个示例，代码扩展性就不考虑了, 估计实际上不会使用
 * 
 * 用户数据格式:
 * 
 * [
 *      [
 *          "id" => 1,
 *          "name" => "peng",
 *          "password" => "123654",
 *          ...
 *      ],
 *      [
 *          "id" => 2,
 *          "name" => "test",
 *          "password" => "123654",
 *          ...
 *      ], ...
 * ]
 * 
 * @author lzpeng <liuzhanpeng@gmail.com>
 */
class NativeArrayUserProvider implements UserProviderInterface
{
    /**
     * 保存用户信息的数组
     *
     * @var array
     */
    private $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @inheritDoc
     */
    public function findById($id)
    {
        foreach ($this->config as $item) {
            if ($item['id'] == $id) {
                return new GenericUser($item);
            }
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function findByCredentials(array $credentials)
    {
        foreach ($this->config as $item) {
            $pass = false;
            foreach ($credentials as $key => $val) {
                if ($key == 'password') {
                    continue;
                }
                if (!isset($item[$key]) || $item[$key] !== $val) {
                    throw new InvalidCredentialException('用户名或密码错误');
                }
                $pass = true;
            }
            if ($pass) {
                return new GenericUser($item);
            }
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function validateCredentials(UserInterface $user, array $credentials)
    {
        if (strcmp($user->password, $credentials['password']) !== 0) {
            throw new InvalidCredentialException('用户名或密码错误');
        }
    }
}
