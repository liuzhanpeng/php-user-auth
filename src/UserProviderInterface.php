<?php

namespace Lzpeng\Auth;

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
     * @throws Exceptions\Exception
     */
    public function findById($id);

    /**
     * 通过用户凭证获取用户对象
     *
     * @param array $credentials 用户凭证
     * @return UserInterface|null
     * @throws Exceptions\Exception
     */
    public function findByCredentials(array $credentials);

    /**
     * 检查用户凭证是否有效 
     *
     * @param UserInterface $user　用户身份对象
     * @param array $credentials 用户凭证
     * @return void
     * @throws Exceptions\Exception
     */
    public function validateCredentials(UserInterface $user, array $credentials);
}
