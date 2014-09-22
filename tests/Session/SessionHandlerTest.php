<?php

use Mockery as m;
use Illuminate\Config\Repository;
use Illuminate\Config\FileLoader;
use Illuminate\Filesystem\Filesystem;
use Ytake\LaravelVoltDB\ClientConnection;

class SessionHandlerTest extends \PHPUnit_Framework_TestCase
{
    /** @var  \Ytake\LaravelVoltDB\Session\VoltDBSessionHandler */
    protected $session;

    public function setUp()
    {
        parent::setUp();
        $config = [
            'driver'    => 'voltdb',
            'host'      => 'localhost',
            'username'  => '',
            'password'  => '',
            'port' => 21212
        ];
        $filePath = PATH;
        $fileLoad = new FileLoader(new Filesystem(), $filePath);
        $repo = new Repository($fileLoad, 'config');
        $repo['auth.table'] = 'users';
        $repo->package('laravel-voltdb', PATH, 'laravel-voltdb');

        $client = new ClientConnection(
            new \Ytake\VoltDB\Client(
                new \VoltClient,
                new \Ytake\VoltDB\Parse
            ), $config);
        $this->session = new \Ytake\LaravelVoltDB\Session\VoltDBSessionHandler(
            $client, $repo
        );
    }

    public function testInstance()
    {
        $this->assertInstanceOf("\Ytake\LaravelVoltDB\Session\VoltDBSessionHandler", $this->session);
    }

    public function testSession()
    {
        $this->assertSame(true, $this->session->open(PATH, 'testing'));
        $this->assertSame(true, $this->session->close());
        $sessionId = md5(rand() . time());
        $this->assertNull($this->session->write($sessionId, 'testing'));
        $this->assertSame('testing', $this->session->read($sessionId));
        $this->assertInstanceOf("\Ytake\LaravelVoltDB\Session\VoltDBSessionHandler", $this->session->setExists(true));
        $this->assertNull($this->session->write($sessionId, 'testing2'));
        $this->assertNull($this->session->gc(120));
        $this->assertNull($this->session->destroy($sessionId));
        $this->assertNull($this->session->read($sessionId));
    }
}