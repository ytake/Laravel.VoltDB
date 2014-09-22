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
    /** @var \Illuminate\Encryption\Encrypter */
    protected $encrypt;
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
        $this->encrypt = new \Illuminate\Encryption\Encrypter('testing');

        $this->clientMock = m::mock("Ytake\LaravelVoltDB\ClientConnection");
        $this->cache = new \Ytake\LaravelVoltDB\Cache\VoltDBStore(
            $this->clientMock,
            $this->encrypt,
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

    public function testCachePut()
    {
        $this->clientMock->shouldReceive('procedure')->andReturnNull();
        $this->cache->put('key', 'testing', 120);
        $this->assertNull($this->cache->forever('key', 'testing'));
    }

    public function testCacheGet()
    {
        $this->clientMock->shouldReceive('procedure')->andReturn(
            [
                [
                    'value' => $this->encrypt->encrypt('testing'),
                    'expiration' => time() + (7 * 24 * 60 * 60)
                ]
            ]
        );
        $this->assertSame('testing', $this->cache->get('key'));

    }
} 