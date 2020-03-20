<?php

namespace Lzpeng\Auth\Tests;

use Lzpeng\Auth\Event\Event;
use Lzpeng\Auth\Event\EventListenerInterface;
use Lzpeng\Auth\Event\EventManager;
use Lzpeng\Auth\Exception\EventException;
use PHPUnit\Framework\TestCase;

class EventManagerTest extends TestCase
{
    protected function setUp(): void
    {
    }

    public function testConstruct()
    {
        $manager = new EventManager();

        return $manager;
    }

    /**
     * @depends clone testConstruct
     */
    public function testAddListenerWithWrong($manager)
    {
        $this->expectException(EventException::class);
        $manager->addListener('login_success', null);
    }

    /**
     * @depends clone testConstruct
     */
    public function testAddListener($manager)
    {
        $listener = $this->getMockBuilder(EventListenerInterface::class)->getMock();

        $manager->addListener('login_success', $listener);

        $this->assertCount(1, $manager->events()['login_success']);
        $this->assertSame($listener, $manager->events()['login_success'][0]);

        $listener2 = $this->getMockBuilder(EventListenerInterface::class)->getMock();

        $manager->addListener('login_success', $listener2);

        $this->assertCount(2, $manager->events()['login_success']);
        $this->assertSame($listener2, $manager->events()['login_success'][1]);

        $listener3 = function (Event $event) {
        };

        $manager->addListener('login_success', $listener3);
        $this->assertCount(3, $manager->events()['login_success']);

        $manager->removeListener('login_success', $listener);

        $this->assertCount(2, $manager->events()['login_success']);

        $manager->removeListener('login_success');
        $this->assertCount(0, $manager->events()['login_success']);

        return $manager;
    }

    /**
     * @depends clone testAddListener
     */
    public function testRemoveListenerWithWrongName($manager)
    {
        $this->expectException(EventException::class);
        $manager->removeListener('wrong_event');
    }

    /**
     * @depends clone testConstruct
     */
    public function testDispatch($manager)
    {
        $listener = $this->getMockBuilder(EventListenerInterface::class)
            ->setMethods(['handle'])
            ->getMock();

        $listener->expects($this->once())
            ->method('handle')
            ->with($this->callback(function (Event $event) {
                return $event->result === true;
            }));

        $listener2 = function (Event $event) {
            echo 'closure handle';
        };

        $this->expectOutputString('closure handle');

        $manager->addListener('login_success', $listener);
        $manager->addListener('login_success', $listener2);

        $manager->dispatch('login_success', new Event([
            'result' => true,
        ]));
    }
}
