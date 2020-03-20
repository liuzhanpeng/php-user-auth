<?php

namespace Lzpeng\Auth;

use Lzpeng\Auth\Event\EventableInterface;

/**
 * 具有事件处理能力的认证器接口
 * 
 * @author lzpeng <liuzhanpeng@gmail.com>
 */
interface EventableAuthenticatorInterface extends AuthenticatorInterface, EventableInterface
{
    /**
     * 登录前事件
     */
    const EVENT_LOGIN_BEFORE = 'login_before';

    /**
     * 登录成功事件
     */
    const EVENT_LOGIN_SUCCESS = 'login_success';

    /**
     * 登录失败事件
     */
    const EVENT_LOGIN_FAILURE = 'login_failure';

    /**
     * 登出前事件
     */
    const EVENT_LOGOUT_BEFORE = 'logout_before';

    /**
     * 登录后事件
     */
    const EVENT_LOGUT_AFTER = 'logout_after';
}
