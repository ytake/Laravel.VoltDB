<?php

use Mockery AS m;
use Illuminate\Config\Repository;
use Illuminate\Config\FileLoader;
use Illuminate\Filesystem\Filesystem;

class VoltDBStoreTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Ytake\LaravelVoltDB\Cache\VoltDBStore  */
    protected $cache;

    protected $clientMock;
    public function tearDown()
    {
        m::close();
    }

    public function setUp()
    {
        parent::setUp();

        $filePath = realpath(null);
        $fileLoad = new FileLoader(new Filesystem(), $filePath);
        $repo = new Repository($fileLoad, 'test');
        $repo->package('laravel-voltdb', realpath(null), 'laravel-voltdb');
        $encrypt = new \Illuminate\Encryption\Encrypter('testing');

        $this->clientMock = m::mock("Ytake\LaravelVoltDB\Client");
        $this->cache = new \Ytake\LaravelVoltDB\Cache\VoltDBStore(
            $this->clientMock,
            $encrypt,
            $repo
        );
    }

    public function testInstance()
    {
        $this->assertInstanceOf('Ytake\LaravelVoltDB\Cache\VoltDBStore', $this->cache);
    }

    public function testGetEncryptInstance()
    {
        $this->assertInstanceOf('Illuminate\Encryption\Encrypter', $this->cache->getEncrypter());
    }

    public function testPrefix()
    {
        $this->assertNull($this->cache->getPrefix());
    }

    public function testFlush()
    {
       $this->clientMock->shouldReceive('procedure')->once()->andReturnNull();
       $this->assertNull($this->cache->flush());
    }

    /**
     * @expectedException \LogicException
     */
    public function testIncrement()
    {
        $this->cache->increment('testing', 1);
    }

    /**
     * @expectedException \LogicException
     */
    public function testDecrement()
    {
        $this->cache->decrement('testing', 1);
    }
} 