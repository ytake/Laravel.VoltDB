<?php

use Mockery as m;
use Ytake\LaravelVoltDB\ClientConnection;

class ClientConnectionTest extends TestCase
{
    /** @var \Ytake\LaravelVoltDB\ClientConnection */
    protected $client;

    public function tearDown()
    {
        m::close();
    }
    protected function setUp()
    {
        parent::setUp();
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
            ),
            $config,
            new \Illuminate\Events\Dispatcher()
        );
    }

    public function testClientInstance()
    {
        $this->assertInstanceOf('Ytake\LaravelVoltDB\ClientConnection', $this->client);
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

    public function testSelect()
    {
        $this->assertInternalType('array', $this->client->execute("DELETE FROM users"));
        $this->assertNull($this->client->selectOne("SELECT * FROM users"));
        $this->assertNull($this->client->select("SELECT * FROM users"));
    }

    public function testConvert()
    {
        $int = $this->client->convertType("SMALLINT", 1);
        $this->assertSame(1, $int);
        $this->assertInternalType('integer', $int);
        $string = $this->client->convertType("VARCHAR", "testing");
        $this->assertSame("'testing'", $string);
        $this->assertInternalType("string", $string);
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
        $this->client = new ClientConnection($clientMock, $config, new \Illuminate\Events\Dispatcher());
    }
} 