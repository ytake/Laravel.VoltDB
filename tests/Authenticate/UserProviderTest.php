<?php

use Mockery as m;
use Illuminate\Config\Repository;
use Illuminate\Config\FileLoader;
use Illuminate\Filesystem\Filesystem;

class UserProviderTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Ytake\LaravelVoltDB\Authenticate\VoltDBUserProvider  */
    protected $provider;
    /** @var array  */
    protected $array = [
        'USER_ID' => 1,
        'USERNAME' => 'testing',
        'PASSWORD' => 'testing',
        'REMEMBER_TOKEN' => 'remember',
        'CREATED_AT' => '1970-00-00 00:00:00'
    ];

    public function tearDown()
    {
        m::close();
    }

    public function setUp()
    {
        parent::setUp();
        $filePath = realpath('../');
        $fileLoad = new FileLoader(new Filesystem(), $filePath);
        $repo = new Repository($fileLoad, 'test');
        $repo->package('laravel-voltdb', realpath(null), 'laravel-voltdb');

        $clientMock = m::mock("Ytake\LaravelVoltDB\Client");

        $clientMock->shouldReceive('procedure')->andReturn([]);
        $this->provider = new \Ytake\LaravelVoltDB\Authenticate\VoltDBUserProvider(
            $clientMock, new \Illuminate\Hashing\BcryptHasher, $repo
        );
    }

    public function testInstance()
    {
        $this->assertInstanceOf("Ytake\LaravelVoltDB\Authenticate\VoltDBUserProvider", $this->provider);
    }

    public function testRetrieveById()
    {
        $this->assertInstanceOf(
            "Ytake\LaravelVoltDB\Authenticate\VoltDBUser", $this->provider->retrieveById(1)
        );
    }

    public function testRetrieveByToken()
    {
        $this->assertInstanceOf(
            "Ytake\LaravelVoltDB\Authenticate\VoltDBUser", $this->provider->retrieveByToken(1, "")
        );
    }
} 