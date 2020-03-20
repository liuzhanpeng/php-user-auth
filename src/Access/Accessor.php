<?php

namespace Lzpeng\Auth\Access;

use Lzpeng\Auth\Event\Event;
use Lzpeng\Auth\UserInterface;
use Lzpeng\Auth\Exception\Exception;

/**
 * 访问控制器
 * 
 * @author lzpeng <liuzhanpeng@gmail.com>
 */
class Accessor implements EventableAccessorInterface
{
    /**
     * 事件管理器
     *
     * @var \Lzpeng\Auth\Event\EventManagerInterface
     */
    private $eventManager;

    /**
     * 权限资源提供器
     *
     * @var ResourceProviderInterface
     */
    private $resourceProvider;

    /**
     * 返回事件管理器
     *
     * @return EventManagerInterface
     * @throws Exception
     */
    protected function getEventManager()
    {
        if (is_null($this->eventManager)) {
            throw new Exception('访问控制器未设置事件管理器');
        }

        return $this->eventManager;
    }

    /**
     * @inheritDoc
     */
    public function setEventManager($eventManager)
    {
        $this->eventManager = $eventManager;
    }

    /**
     * @inheritDoc
     */
    public function setResourceProvider(ResourceProviderInterface $resourceProvider)
    {
        $this->resourceProvider = $resourceProvider;
    }

    /**
     * 返回权限资源提供器
     *
     * @return ResourceProviderInterface
     * @throws Exception
     */
    public function getResourceProvider()
    {
        if (is_null($this->resourceProvider)) {
            throw new Exception('认证器未设置权限资源提供器');
        }

        return $this->resourceProvider;
    }

    /**
     * @inheritDoc
     */
    public function isAllowed(UserInterface $user, string $resourceId)
    {
        $this->getEventManager()->dispatch(self::EVENT_ACCESS_BEFORE, new Event([
            'resourceId' => $resourceId,
            'user' => $user,
        ]));

        $provider = $this->getResourceProvider();

        $result = false;
        foreach ($provider->getResources($user) as $resource) {
            if ($resource->id() === $resourceId) {
                $result = true;
                break;
            }
        }

        $this->getEventManager()->dispatch(self::EVENT_ACCESS_AFTER, new Event([
            'resourceId' => $resourceId,
            'user' => $user,
            'result' => $result,
        ]));

        return $result;
    }
}
