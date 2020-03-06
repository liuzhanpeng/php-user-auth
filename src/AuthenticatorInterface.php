<?php

namespace Lzpeng\Auth;

use Lzpeng\Auth\Contracts\UserInterface;

/**
 * 认证器接口
 * 
 * @author lzpeng <liuzhanpeng@gmail.com>
 */
interface AuthenticatorInterface
{
    /**
     * 用户登录
     *
     * @param array $credentials 用户凭证
     * @return mixed
     * @throws Exceptions\Exception
     */
    public function login(array $credentials);

    /**
     * 是否已登录
     *
     * @return boolean
     */
    public function isLogined();

    /**
     * 用户用户唯一身份标识
     * 标识可为id、帐号等
     * 如果未登录返回null
     *
     * @return mixed|null
     */
    public function getId();

    /**
     * 获取用户身份对象
     * 如果未登录返回null
     *
     * @return UserInterface|null
     */
    public function getUser();

    /**
     * 设置用户身份对象
     *
     * @param UserInterface $user
     * @return void
     * @throws Exceptions\Exception
     */
    public function setUser(UserInterface $user);

    /**
     * 用户登出
     *
     * @return void
     * @throws Exceptions\Exception
     */
    public function logout();
}
