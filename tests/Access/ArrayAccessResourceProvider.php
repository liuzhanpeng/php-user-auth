<?php

namespace Lzpeng\Tests\Access;

use Lzpeng\Auth\Access\Resource;
use Lzpeng\Auth\Contracts\AccessResourceProviderInterface;

class ArrayAccessResourceProvider implements AccessResourceProviderInterface
{
    private $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function getAccessResources()
    {
        $list = [];
        foreach ($this->config as $item) {
            $list[] = new Resource($item);
        }

        return $list;
    }
}
