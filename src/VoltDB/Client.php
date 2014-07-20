<?php
namespace Ytake\LaravelVoltDB;

use Ytake\VoltDB\SystemProcedure;
use Ytake\VoltDB\Client as BaseClient;
use Illuminate\Database\Connection;
use Illuminate\Support\Facades\Event AS IlluminateEvent;

/**
 * Class Client
 * @package Ytake\LaravelVoltDB
 * @author  yuuki.takezawa<yuuki.takezawa@comnect.jp.net>
 * @license http://opensource.org/licenses/MIT MIT
 */
class Client extends Connection
{

    /** @var BaseClient */
    protected $client;

    /** @var array */
    protected $config;

    /** @var \Ytake\VoltDB\Client */
    protected $voltConnection;

    /**
     * @param BaseClient $client
     * @param array $config
     */
    public function __construct(BaseClient $client, array $config)
    {
        $this->client = $client;
        $this->config = $config;
        $this->voltConnection = $this->client->connect($config);
    }

    /**
     * use @AdHoc query
     * @param  string  $query
     * @param  $bindings not supported
     * @return mixed
     */
    public function selectOne($query, $bindings = null)
    {
        return $this->voltConnection->selectOne($query);
    }

    /**
     * use @AdHoc query
     * @param string $query
     * @param null $bindings
     * @return array|void
     */
    public function select($query, $bindings = null)
    {
        return $this->voltConnection->select($query);
    }

    /**
     * use @AdHoc query
     * insert, update, delete
     * @param $query
     * @return array
     * @throws \Ytake\VoltDB\Exception\ResponseErrorException
     */
    public function exec($query)
    {
        $voltDB = $this->voltConnection->getClient();
        $response = $voltDB->invoke(SystemProcedure::AD_HOC, [$query]);
        return $this->voltConnection->getResult($response);
    }

    /**
     * use stored procedure
     * @param $name
     * @param array $params
     * @return array
     */
    public function procedure($name, array $params = [])
    {
        return $this->voltConnection->procedure($name, $params);
    }

    /**
     * get VoltClient Instance
     * @return \Ytake\VoltDB\Client
     */
    public function getVoltClient()
    {
        return $this->voltConnection;
    }

    /**
     * @return string
     */
    public function getDriverName()
    {
        return 'voltdb';
    }

    /**
     * convert value
     * @param string $type
     * @param string $value
     * @return string|int|float
     *
     * @deprecated
     */
    public function convertType($type, $value)
    {
        switch($type) {
            case "TINYINT":
            case "SMALLINT":
            case "INTEGER":
            case "BIGINT":
            case "TIMESTAMP":
                return (int) $value;
                break;
            case "FLOAT":
            case "DECIMAL":
                return (float) $value;
                break;
            default:
                return "'{$value}'";
                break;
        }
    }

    /**
     * not supported functions
     */

    public function table($table)
    {
        IlluminateEvent::fire('voltdb.not_supported', [__FUNCTION__]);
    }

    public function raw($value)
    {
        IlluminateEvent::fire('voltdb.not_supported', [__FUNCTION__]);
    }

    public function insert($query, $bindings = array())
    {
        IlluminateEvent::fire('voltdb.not_supported', [__FUNCTION__]);
    }

    public function update($query, $bindings = array())
    {
        IlluminateEvent::fire('voltdb.not_supported', [__FUNCTION__]);
    }

    public function delete($query, $bindings = array())
    {
        IlluminateEvent::fire('voltdb.not_supported', [__FUNCTION__]);
    }


    public function statement($query, $bindings = array())
    {
        IlluminateEvent::fire('voltdb.not_supported', [__FUNCTION__]);
    }

    public function affectingStatement($query, $bindings = array())
    {
        IlluminateEvent::fire('voltdb.not_supported', [__FUNCTION__]);
    }

    public function unprepared($query)
    {
        IlluminateEvent::fire('voltdb.not_supported', [__FUNCTION__]);
    }

    public function prepareBindings(array $bindings)
    {
        IlluminateEvent::fire('voltdb.not_supported', [__FUNCTION__]);
    }

    public function transaction(\Closure $callback)
    {
        IlluminateEvent::fire('voltdb.not_supported', [__FUNCTION__]);
    }

    public function beginTransaction()
    {
        IlluminateEvent::fire('voltdb.not_supported', [__FUNCTION__]);
    }

    public function commit()
    {
        IlluminateEvent::fire('voltdb.not_supported', [__FUNCTION__]);
    }

    public function rollBack()
    {
        IlluminateEvent::fire('voltdb.not_supported', [__FUNCTION__]);
    }

    public function transactionLevel()
    {
        IlluminateEvent::fire('voltdb.not_supported', [__FUNCTION__]);
    }

    public function pretend(\Closure $callback)
    {
        IlluminateEvent::fire('voltdb.not_supported', [__FUNCTION__]);
    }
}