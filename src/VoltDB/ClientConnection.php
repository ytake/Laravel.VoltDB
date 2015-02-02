<?php
namespace Ytake\LaravelVoltDB;

use Ytake\VoltDB\SystemProcedure;
use Illuminate\Database\Connection;
use Ytake\VoltDB\Client as BaseClient;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;

/**
 * Class ClientConnection
 * @package Ytake\LaravelVoltDB
 * @author  yuuki.takezawa<yuuki.takezawa@comnect.jp.net>
 * @license http://opensource.org/licenses/MIT MIT
 */
class ClientConnection extends Connection
{

    /** @var BaseClient */
    protected $client;

    /** @var array */
    protected $config;

    /** @var DispatcherContract  */
    protected $dispatcher;

    /** @var \Ytake\VoltDB\Client */
    protected $voltConnection;

    /**
     * @param BaseClient $client
     * @param array $config
     * @param DispatcherContract $dispatcher
     * @throws \Ytake\VoltDB\Exception\ConnectionErrorException
     */
    public function __construct(BaseClient $client, array $config, DispatcherContract $dispatcher)
    {
        $this->client = $client;
        $this->config = $config;
        $this->voltConnection = $this->client->connect($config);
        $this->dispatcher = $dispatcher;
    }

    /**
     * use @AdHoc query
     * @param  string  $query
     * @param  $bindings not supported
     * @return mixed
     *
     * @deprecated
     */
    public function selectOne($query, $bindings = null)
    {
        return $this->voltConnection->executeOne($query);
    }

    /**
     * use @AdHoc query
     * @param string $query
     * @param null $bindings
     * @return array|void
     *
     * @deprecated
     */
    public function select($query, $bindings = null)
    {
        return $this->voltConnection->execute($query);
    }

    /**
     * use @AdHoc query
     * insert, update, delete
     * @param $query
     * @return array
     * @throws \Ytake\VoltDB\Exception\ResponseErrorException
     */
    public function execute($query)
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

    /**
     * @param string $table
     * @return void
     */
    public function table($table)
    {
        $this->dispatcher->fire('voltdb.not_supported', [__FUNCTION__]);
    }

    /**
     * @param mixed $value
     * @return void
     */
    public function raw($value)
    {
        $this->dispatcher->fire('voltdb.not_supported', [__FUNCTION__]);
    }

    public function insert($query, $bindings = array())
    {
        $this->dispatcher->fire('voltdb.not_supported', [__FUNCTION__]);
    }

    public function update($query, $bindings = array())
    {
        $this->dispatcher->fire('voltdb.not_supported', [__FUNCTION__]);
    }

    public function delete($query, $bindings = array())
    {
        $this->dispatcher->fire('voltdb.not_supported', [__FUNCTION__]);
    }


    public function statement($query, $bindings = array())
    {
        $this->dispatcher->fire('voltdb.not_supported', [__FUNCTION__]);
    }

    public function affectingStatement($query, $bindings = array())
    {
        $this->dispatcher->fire('voltdb.not_supported', [__FUNCTION__]);
    }

    public function unprepared($query)
    {
        $this->dispatcher->fire('voltdb.not_supported', [__FUNCTION__]);
    }

    public function prepareBindings(array $bindings)
    {
        $this->dispatcher->fire('voltdb.not_supported', [__FUNCTION__]);
    }

    public function transaction(\Closure $callback)
    {
        $this->dispatcher->fire('voltdb.not_supported', [__FUNCTION__]);
    }

    public function beginTransaction()
    {
        $this->dispatcher->fire('voltdb.not_supported', [__FUNCTION__]);
    }

    public function commit()
    {
        $this->dispatcher->fire('voltdb.not_supported', [__FUNCTION__]);
    }

    public function rollBack()
    {
        $this->dispatcher->fire('voltdb.not_supported', [__FUNCTION__]);
    }

    public function transactionLevel()
    {
        $this->dispatcher->fire('voltdb.not_supported', [__FUNCTION__]);
    }

    public function pretend(\Closure $callback)
    {
        $this->dispatcher->fire('voltdb.not_supported', [__FUNCTION__]);
    }
}
