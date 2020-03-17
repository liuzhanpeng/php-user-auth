<?php

namespace Lzpeng\Auth;

use Lzpeng\Auth\Event\EventManagerInterface;

/**
 * 认证事件接口
 * 实现该接口的认证器将拥有相关事件处理能力
 * 
 * @author lzpeng <liuzhanpeng@gmail.com>
 */
interface AuthEventInterface
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

    /**
     * 访问权限资源前事件
     */
    const EVENT_ACCESS_BEFORE = 'access_before';

    /**
     * 访问权限资源后事件
     */
    const EVENT_ACCESS_AFTER = 'access_after';

    /**
     * 设置事件管理器
     *
     * @param EventManagerInterface $eventManager 事件管理器
     * @return void
     */
    public function setEventManager(EventManagerInterface $eventManager);

    /**
     * 返回事件管理器
     *
     * @return EventManagerInterface
     */
    public function getEventManager();
}
