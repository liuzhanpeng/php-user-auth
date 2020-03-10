<?php

namespace Lzpeng\Auth\Contracts;

/**
 * 用户身份对象提供器接口
 * 
 * @author lzpeng <liuzhanpeng@gmail.com>
 */
interface UserProviderInterface
{
    /**
     * 通过用户身份标识获取用户对象
     *
     * @param mixed $id 用户身份标识
     * @return UserInterface|null
     * @throws \Lzpeng\Auth\Exceptions\AuthException
     */
    public function findById($id);

    /**
     * 通过用户凭证获取用户对象
     *
     * @param array $credentials 用户凭证
     * @return UserInterface|null
     * @throws \Lzpeng\Auth\Exceptions\AuthException
     */
    public function findByCredentials(array $credentials);

    /**
     * 检查用户凭证是否有效 
     * 不使用返回ture/false, 而是采用抛出异常方式主要是为了能将具体错误信息抛到业务层实现不现的处理逻辑
     *
     * @param UserInterface $user　用户身份对象
     * @param array $credentials 用户凭证
     * @return void
     * @throws \Lzpeng\Auth\Exceptions\AuthException
     */
    public function validateCredentials(UserInterface $user, array $credentials);
}
