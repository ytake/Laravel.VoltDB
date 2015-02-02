<?php

use Mockery as m;
use Ytake\LaravelVoltDB\ClientConnection;

class UserProviderTest extends TestCase
{
    /** @var \Ytake\LaravelVoltDB\Authenticate\VoltDBUserProvider  */
    protected $provider;
    /** @var \Ytake\LaravelVoltDB\ClientConnection  */
    protected $client;
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
        $config = [
            'driver' => 'voltdb',
            'host' => 'localhost',
            'username' => '',
            'password' => '',
            'port' => 21212
        ];
        $this->client = new ClientConnection(
            new \Ytake\VoltDB\Client(
                new \VoltClient,
                new \Ytake\VoltDB\Parse
            ),
            $config,
            new \Illuminate\Events\Dispatcher()
        );
        $this->config->set('auth.table', 'USERS');
        $this->provider = new \Ytake\LaravelVoltDB\Authenticate\VoltDBUserProvider(
            $this->client, new \Illuminate\Hashing\BcryptHasher, $this->config
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