<?php

namespace Lzpeng\Auth\Tests;

use Lzpeng\Auth\Exception\Exception;
use Lzpeng\Auth\Users\GenericUser;
use PHPUnit\Framework\TestCase;

class GenericUserTest extends TestCase
{
    protected function setUp(): void
    {
    }

    public function testConstruct()
    {
        $user = new GenericUser([
            'id' => 1,
            'name' => 'peng',
            'remark' => 'test',
        ]);

        return $user;
    }

    public function testConstructWithWrongIdKey()
    {
        $user = new GenericUser([
            'id' => 1,
            'name' => 'peng',
            'remark' => 'test',
        ], 'wrongidkey');

        return $user;
    }

    /**
     * @depends clone testConstruct
     */
    public function testId($user)
    {
        $this->assertEquals(1, $user->id());
    }

    /**
     * @depends clone testConstructWithWrongIdKey
     */
    public function testIdWithWrongKey($user)
    {
        $this->expectException(Exception::class);
        $user->id();
    }

    /**
     * @depends clone testConstruct
     */
    public function testArrayAccessMethods($user)
    {
        $this->assertEquals('peng', $user['name']);

        $this->assertTrue(isset($user['name']));
        $this->assertFalse(isset($user['wrongProperty']));

        $user['age'] = 30;
        $this->assertTrue(isset($user['age']));
        $this->assertEquals(30, $user['age']);

        unset($user['age']);
        $this->assertFalse(isset($user['age']));
    }

    /**
     * @depends clone testConstruct
     */
    public function testMagicMethods($user)
    {
        $this->assertEquals('peng', $user->name);

        $this->assertTrue(isset($user->name));
        $this->assertFalse(isset($user->wrongProperty));

        $user->age = 30;
        $this->assertTrue(isset($user->age));
        $this->assertEquals(30, $user->age);

        unset($user->age);
        $this->assertFalse(isset($user->age));
    }
}
