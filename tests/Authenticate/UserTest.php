<?php

use Mockery as m;
use Illuminate\Config\Repository;
use Illuminate\Config\FileLoader;
use Illuminate\Filesystem\Filesystem;

class UserTest extends \PHPUnit_Framework_TestCase
{
    protected $array = [
        'USER_ID' => 1,
        'username' => 'testing',
        'PASSWORD' => 'testing',
        'REMEMBER_TOKEN' => 'remember',
        'created_at' => '1970-00-00 00:00:00'
    ];

    /** @var  \Ytake\LaravelVoltDB\Authenticate\VoltDBUser */
    protected $user;

    public function tearDown()
    {
        m::close();
    }

    public function setUp()
    {
        parent::setUp();
        $filePath = PATH;
        $fileLoad = new FileLoader(new Filesystem(), $filePath);
        $repo = new Repository($fileLoad, 'config');
        $repo->package('laravel-voltdb', PATH, 'laravel-voltdb');
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
        $this->assertSame("REMEMBER_TOKEN", $this->user->getRememberTokenName());
    }

    public function testSetToken()
    {
        $this->user->setRememberToken('testing');
        $this->assertSame('testing', $this->user->REMEMBER_TOKEN);
    }

    public function testProperty()
    {
        $this->assertSame(false, isset($this->user->testing));
        unset($this->user->testing);
        $this->user->testing = 'Laravel.voltDB';
        $this->assertSame('Laravel.voltDB', $this->user->testing);
    }
} 