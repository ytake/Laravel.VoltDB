<?php

use Mockery as m;
use Illuminate\Config\Repository;
use Illuminate\Config\FileLoader;
use Illuminate\Filesystem\Filesystem;

class UserTest extends \PHPUnit_Framework_TestCase
{
    protected $array = [
        'id' => 1,
        'username' => 'testing',
        'password' => 'testing',
        'remember_token' => 'remember',
        'created_at' => '1970-00-00 00:00:00'
    ];

    /** @var  \Ytake\LaravelVoltDB\Authenticate\VoltDBUser */
    protected $user;

    public function setUp()
    {
        parent::setUp();
        $filePath = realpath('../');
        $fileLoad = new FileLoader(new Filesystem(), $filePath);
        $repo = new Repository($fileLoad, 'test');
        $repo->package('laravel-voltdb', realpath(null), 'laravel-voltdb');
        $this->user = new \Ytake\LaravelVoltDB\Authenticate\VoltDBUser($this->array, $repo);
    }

    public function testInstance()
    {
        $this->assertInstanceOf("Ytake\LaravelVoltDB\Authenticate\VoltDBUser", $this->user);
    }

    public function testIdentifier()
    {
        $this->assertSame(1, $this->user->getAuthIdentifier());
    }

    public function testPassword()
    {
        $this->assertSame('testing', $this->user->getAuthPassword());
    }

    public function testToken()
    {
        $this->assertSame('remember', $this->user->getRememberToken());
    }

    public function testTokenName()
    {
        $this->assertSame("remember_token", $this->user->getRememberTokenName());
    }
} 