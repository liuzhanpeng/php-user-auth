<?php

namespace Lzpeng\Auth\Tests;

use Lzpeng\Auth\Access\GenericResource;
use PHPUnit\Framework\TestCase;

class GenericResourceTest extends TestCase
{
    protected function setUp(): void
    {
    }

    public function testConstruct()
    {
        $resource = new GenericResource('resource1', [
            'remark' => 'test resource1'
        ]);

        return $resource;
    }

    /**
     * @depends clone testConstruct
     */
    public function testId($resource)
    {
        $this->assertEquals('resource1', $resource->id());
    }

    /**
     * @depends clone testConstruct
     */
    public function testArrayAccessMethods($resource)
    {
        $this->assertEquals('test resource1', $resource['remark']);

        $this->assertTrue(isset($resource['remark']));
        $this->assertFalse(isset($resource['wrongProperty']));

        $resource['tips'] = 'tips';
        $this->assertTrue(isset($resource['tips']));
        $this->assertEquals('tips', $resource['tips']);

        unset($resource['tips']);
        $this->assertFalse(isset($resource['tips']));
    }

    /**
     * @depends clone testConstruct
     */
    public function testMagicMethods($resource)
    {
        $this->assertEquals('test resource1', $resource->remark);

        $this->assertTrue(isset($resource->remark));
        $this->assertFalse(isset($resource->wrongProperty));

        $resource->tips = 'tips';
        $this->assertTrue(isset($resource->tips));
        $this->assertEquals('tips', $resource->tips);

        unset($resource->tips);
        $this->assertFalse(isset($resource->tips));
    }
}
