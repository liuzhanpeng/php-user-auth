<?php

namespace Lzpeng\Auth\Tests;

use Lzpeng\Auth\Exception\InvalidCredentialException;
use Lzpeng\Auth\UserInterface;
use Lzpeng\Auth\UserProviders\NativeArrayUserProvider;
use PHPUnit\Framework\TestCase;

class NativeArrayUserProviderTest extends TestCase
{
    private $config;

    protected function setUp(): void
    {
        $this->config = [
            [
                'id' => 1,
                'name' => 'peng',
                'password' => '123654',
            ],
            [
                'id' => 2,
                'name' => 'test',
                'password' => '123654',
            ],
        ];
    }

    public function testConstruct()
    {
        $provider = new NativeArrayUserProvider($this->config);

        return $provider;
    }

    /**
     * @depends clone testConstruct
     */
    public function testFindById($provider)
    {
        $user = $provider->findById(-1);

        $this->assertNull($user);

        $user = $provider->findById(1);

        $this->assertInstanceOf(UserInterface::class, $user);

        $this->assertEquals('peng', $user->name);
    }

    /**
     * @depends clone testConstruct
     */
    public function testFindByCredentialsWithWrong($provider)
    {
        $this->expectException(InvalidCredentialException::class);
        $provider->findByCredentials([
            'name' => 'wrong',
        ]);
    }

    /**
     * @depends clone testConstruct
     */
    public function testFindByCredentialsWithNotExists($provider)
    {
        $user = $provider->findByCredentials([]);

        $this->assertNull($user);
    }

    /**
     * @depends clone testConstruct
     */
    public function testFindByCredentials($provider)
    {
        $user = $provider->findByCredentials([
            'name' => 'peng',
            'password' => '123654'
        ]);

        $this->assertInstanceOf(UserInterface::class, $user);

        $this->assertEquals('peng', $user->name);

        return $user;
    }

    /**
     * @depends clone testConstruct
     */
    public function testValidateCredentialsWithWrong($provider)
    {
        $user = $provider->findByCredentials([
            'name' => 'peng',
        ]);

        $this->expectException(InvalidCredentialException::class);
        $provider->validateCredentials($user, [
            'name' => 'peng',
            'password' => 'wrong',
        ]);
    }

    /**
     * @depends clone testConstruct
     */
    public function testValidateCredentials($provider)
    {
        $user = $provider->findByCredentials([
            'name' => 'peng',
        ]);

        $provider->validateCredentials($user, [
            'name' => 'peng',
            'password' => '123654',
        ]);
    }
}
