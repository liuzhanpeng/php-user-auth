<?php

namespace Lzpeng\Auth\Tests;

use Lzpeng\Auth\Event\Event;
use PHPUnit\Framework\TestCase;

class EventTest extends TestCase
{
    private $data;

    protected function setUp(): void
    {
        $this->data = [
            'credentials' => [
                'account' => 'peng',
                'password' => '123654'
            ],
        ];
    }

    public function testConstruct()
    {
        $event = new Event($this->data);

        return $event;
    }

    /**
     * @depends clone testConstruct
     */
    public function testStop($event)
    {
        $this->assertFalse($event->isStopped());

        $event->stop();

        $this->assertTrue($event->isStopped());
    }

    /**
     * @depends clone testConstruct
     */
    public function testArrayAccessMethods($event)
    {
        $this->assertEquals($this->data['credentials'], $event['credentials']);

        $this->assertTrue(isset($event['credentials']));
        $this->assertFalse(isset($event['wrongProperty']));

        $event['remark'] = 'test';
        $this->assertTrue(isset($event['remark']));
        $this->assertEquals('test', $event['remark']);

        unset($event['remark']);
        $this->assertFalse(isset($event['remark']));
    }

    /**
     * @depends clone testConstruct
     */
    public function testMagicMethods($event)
    {
        $this->assertEquals($this->data['credentials'], $event->credentials);

        $this->assertTrue(isset($event->credentials));
        $this->assertFalse(isset($event->wrongProperty));

        $event->remark = 'test';
        $this->assertTrue(isset($event->remark));
        $this->assertEquals('test', $event->remark);

        unset($event->remark);
        $this->assertFalse(isset($event->remark));
    }
}
