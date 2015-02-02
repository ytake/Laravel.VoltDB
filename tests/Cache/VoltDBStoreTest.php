<?php

use Mockery AS m;

class VoltDBStoreTest extends TestCase
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

    protected function setUp()
    {
        parent::setUp();

        $this->encrypt = new \Illuminate\Encryption\Encrypter('testingtestingtesting123');

        $this->clientMock = m::mock("Ytake\LaravelVoltDB\ClientConnection");
        $this->cache = new \Ytake\LaravelVoltDB\Cache\VoltDBStore(
            $this->clientMock,
            $this->encrypt,
            $this->config
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
        $this->cache->forever('key', 'testing');
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