<?php

namespace Lzpeng\Auth\Authenticators;

use Lzpeng\Auth\Contracts\AccessInterface;
use Lzpeng\Auth\Contracts\AccessResourceProviderInterface;
use Lzpeng\Auth\Exceptions\AccessException;
use Lzpeng\Auth\Exceptions\Exception;

/**
 * 实现带权限资源访问功能的抽象认证器
 * 
 * @author lzpeng <liuzhanpeng@gmail.com>
 */
abstract class AbstractAccessableAuthenticator extends AbstractAuthenticator implements AccessInterface
{
    /**
     * 权限资源提供器
     *
     * @var \Lzpeng\Auth\Contracts\AccessResourceProviderInterface
     */
    private $accessResourceProvider;

    /**
     * @inheritDoc
     */
    public function setAccessSourceProvider(AccessResourceProviderInterface $accessResourceProvider)
    {
        $this->accessResourceProvider = $accessResourceProvider;
    }

    /**
     * @inheritDoc
     */
    public function getAccessSourceProvider()
    {
        return $this->accessResourceProvider;
    }

    /**
     * @inheritDoc
     */
    public function isAllowed($resourceId)
    {
        if (!$this->isLogined()) {
            throw new AccessException('用户还未登录认证');
        }

        $this->getEventManager()->trigger(self::EVENT_ACCESS_BEFORE, [
            'resourceId' => $resourceId,
            'user' => $this->user(),
        ]);

        $provider = $this->getAccessSourceProvider();
        if (is_null($provider)) {
            throw new Exception('找不到权限资源提供器');
        }

        $result = false;
        foreach ($provider->getAccessResources($this->user()) as $resource) {
            if ($resource->id() === $resourceId) {
                $result = true;
                break;
            }
        }

        $this->getEventManager()->trigger(self::EVENT_ACCESS_AFTER, [
            'resourceId' => $resourceId,
            'user' => $this->user(),
            'isAllowed' => $result,
        ]);

        return $result;
    }
}
