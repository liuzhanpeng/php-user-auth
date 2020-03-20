<?php

namespace Lzpeng\Auth\Tests;

use Exception;
use Lzpeng\Auth\Event\EventListenerInterface;

class TestListener implements EventListenerInterface
{
    public function handle(\Lzpeng\Auth\Event\Event $event)
    {
        echo $event->isStopped();
    }
}
