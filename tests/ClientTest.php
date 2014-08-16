<?php

use Mockery as m;
use Ytake\LaravelVoltDB\Client;

class ClientTest extends \PHPUnit_Framework_TestCase
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
        $clientMock = m::mock("Ytake\VoltDB\Client");
        $clientMock->shouldReceive('connect')->once()->andReturn($clientMock);
        $clientMock->shouldReceive('procedure')->once()->andReturn([]);
        $this->client = new Client($clientMock, $config);
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

    /**
     * @expectedException \Ytake\VoltDB\Exception\ConnectionErrorException
     */
    public function testNotConnection()
    {
        $config = [
            'driver'    => 'voltdb',
            'host'      => 'localhost',
            'username'  => '',
            'password'  => '',
            'port' => 8888
        ];
        $clientMock = m::mock("Ytake\VoltDB\Client");
        $clientMock->shouldReceive('connect')->once()->andThrowExceptions([
                new \Ytake\VoltDB\Exception\ConnectionErrorException]);
        $this->client = new Client($clientMock, $config);
    }
} 