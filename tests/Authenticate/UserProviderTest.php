<?php

use Mockery as m;
use Illuminate\Config\Repository;
use Illuminate\Config\FileLoader;
use Illuminate\Filesystem\Filesystem;
use Ytake\LaravelVoltDB\ClientConnection;

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
        $filePath = PATH;
        $fileLoad = new FileLoader(new Filesystem(), $filePath);
        $repo = new Repository($fileLoad, 'config');
        $repo['auth.table'] = 'users';
        $repo->package('laravel-voltdb', PATH, 'laravel-voltdb');
        $config = [
            'driver'    => 'voltdb',
            'host'      => 'localhost',
            'username'  => '',
            'password'  => '',
            'port' => 21212
        ];
        $this->client = new ClientConnection(
            new \Ytake\VoltDB\Client(
                new \VoltClient,
                new \Ytake\VoltDB\Parse
            ), $config);
        $this->provider = new \Ytake\LaravelVoltDB\Authenticate\VoltDBUserProvider(
            $this->client, new \Illuminate\Hashing\BcryptHasher, $repo
        );
    }

    public function testInstance()
    {
        $this->assertInstanceOf("Ytake\LaravelVoltDB\Authenticate\VoltDBUserProvider", $this->provider);
    }

    public function testRetrieveById()
    {
        $this->assertNull($this->provider->retrieveById(1));
    }

    public function testRetrieveByToken()
    {
        $this->assertNull($this->provider->retrieveByToken(1, ""));
    }

    public function testRetrieveByCredentials()
    {
        $this->assertNull($this->provider->retrieveByCredentials(['username' => 'testing']));
    }
} 