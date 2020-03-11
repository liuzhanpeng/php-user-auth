<?php

namespace Lzpeng\Tests\Listeners;

use Lzpeng\Auth\Contracts\EventListenerInterface;

class LogCrendentials implements EventListenerInterface
{
    public function handle($arg)
    {
        file_put_contents('log', $arg['credentials']['name']);
    }
}
