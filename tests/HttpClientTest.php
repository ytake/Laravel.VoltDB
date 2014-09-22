<?php

use Mockery as m;
use Illuminate\Config\Repository;
use Ytake\VoltDB\Parse;
use Illuminate\Config\FileLoader;
use Illuminate\Filesystem\Filesystem;

class HttpClientTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Ytake\LaravelVoltDB\HttpClient */
    protected $client;

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
        $this->client = new \Ytake\LaravelVoltDB\HttpClient($repo, new Parse());
    }

    public function testHttpClientInstance()
    {
        $this->assertInstanceOf('Ytake\LaravelVoltDB\HttpClient', $this->client);
    }

    public function testHttpClient()
    {
        $class = new ReflectionClass($this->client);
        $host = $class->getProperty("host");
        $path = $class->getProperty("path");
        $apiPort = $class->getProperty("apiPort");
        $ssl = $class->getProperty("ssl");
        $host->setAccessible(true);
        $path->setAccessible(true);
        $apiPort->setAccessible(true);
        $ssl->setAccessible(true);
        $this->assertSame('localhost', $host->getValue($this->client));
        $this->assertSame('/api/1.0/', $path->getValue($this->client));
        $this->assertSame(8080, $apiPort->getValue($this->client));
        $this->assertSame(false, $ssl->getValue($this->client));
    }

    public function TestHttpClientVolt()
    {
        $this->assertInternalType('array', $this->client->getParam());
    }
}
