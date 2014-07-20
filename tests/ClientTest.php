<?php

use Ytake\VoltDB\Parse;
use Ytake\LaravelVoltDB\Client;

class ClientTest extends \App\Tests\TestCase
{
    /** @var \Ytake\LaravelVoltDB\Client */
    protected $client;

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
        $this->client = new Client(new \Ytake\VoltDB\Client(new \VoltClient, new Parse), $config);
    }

    public function testClientInstance()
    {
        $this->assertInstanceOf('Ytake\LaravelVoltDB\Client', $this->client);
    }

    public function testVoltClient()
    {
        $this->assertInstanceOf('Ytake\VoltDB\Client', $this->client->getVoltClient());
        $this->assertSame('voltdb', $this->client->getDriverName());
    }

    /**
     * required ext-voltdb
     */
    public function testProcedure()
    {
        $response = $this->client->procedure("@SystemCatalog", ["COLUMNS"]);
        $this->assertInternalType('array', $response);
    }
} 