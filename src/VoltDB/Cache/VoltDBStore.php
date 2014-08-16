<?php
namespace Ytake\LaravelVoltDB\Cache;

use Ytake\LaravelVoltDB\Client;
use Illuminate\Config\Repository;
use Illuminate\Encryption\Encrypter;
use Illuminate\Cache\StoreInterface;

/**
 * Class VoltDBStore
 * @package Ytake\LaravelVoltDB\Cache
 * @author yuuki.takezawa<yuuki.takezawa@comnect.jp.net>
 * @license http://opensource.org/licenses/MIT MIT
 */
class VoltDBStore implements StoreInterface
{

    /** @var Repository */
    protected $config;

    /** @var string  */
    protected $connection;

    /** @var string cache prefix key */
    protected $prefix;

    /** @var \Illuminate\Encryption\Encrypter encrypter instance */
    protected $encrypter;

    /** @var string default procedure */
    protected $cacheFindProcedure = "Cache_find";

    /** @var string default procedure */
    protected $cacheAddProcedure = "Cache_add";

    /** @var string default procedure */
    protected $cacheUpdateProcedure = "Cache_update";

    /** @var string default procedure */
    protected $cacheForgetProcedure = "Cache_forget";

    /** @var string default procedure */
    protected $cacheFlushProcedure = "Cache_flushAll";

    /**
     * @param Client $connection
     * @param Encrypter $encrypter
     * @param Repository $config
     */
    public function __construct(Client $connection, Encrypter $encrypter, Repository $config)
    {
        $this->config = $config;
        $this->connection = $connection;
        $this->encrypter = $encrypter;
        $this->prefix = $this->config->get('cache.prefix');
    }


    /**
     * Retrieve an item from the cache by key.
     *
     * @param  string  $key
     * @return mixed
     */
    public function get($key)
    {
        $prefixed = $this->prefix.$key;
        // stored procedure name
        $cacheFindProcedure = $this->config->get(
            'laravel-voltdb::default.cache.procedure.find', $this->cacheFindProcedure);
        // cache get
        $cache = $this->connection->procedure($cacheFindProcedure, [$prefixed]);

        if(!is_null($cache)) {
            if(is_array($cache)) {
                $cache = (object) array_change_key_case($cache[0]);
            }
            if(time() >= $cache->expiration) {
                $this->forget($key);
                return null;
            }
            return $this->encrypter->decrypt($cache->value);
        }
    }

    /**
     * Store an item in the cache for a given number of minutes.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @param  int     $minutes
     * @return void
     */
    public function put($key, $value, $minutes)
    {
        /**
         * @todo
         * not supported "VARBINARY" php client
         */
        $key = $this->prefix.$key;
        $value = $this->encrypter->encrypt($value);
        $expiration = $this->getTime() + ($minutes * 60);

        $cacheAddProcedure = $this->config->get(
            'laravel-voltdb::default.cache.procedure.add', $this->cacheAddProcedure);

        $cacheUpdateProcedure = $this->config->get(
            'laravel-voltdb::default.cache.procedure.update', $this->cacheUpdateProcedure);
        try {
            $this->connection->procedure($cacheAddProcedure, compact('key', 'value', 'expiration'));
        } catch (\Exception $e) {
            $this->connection->procedure($cacheUpdateProcedure, compact('value', 'expiration', 'key'));
        }
    }

    /**
     * Increment the value of an item in the cache.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return void
     *
     * @throws \LogicException
     */
    public function increment($key, $value = 1)
    {
        throw new \LogicException("Increment operations not supported by this driver.");
    }

    /**
     * Increment the value of an item in the cache.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return void
     *
     * @throws \LogicException
     */
    public function decrement($key, $value = 1)
    {
        throw new \LogicException("Decrement operations not supported by this driver.");
    }

    /**
     * Get the current system time.
     *
     * @return int
     */
    protected function getTime()
    {
        return time();
    }

    /**
     * Store an item in the cache indefinitely.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return void
     */
    public function forever($key, $value)
    {
        $this->put($key, $value, 5256000);
    }

    /**
     * Remove an item from the cache.
     *
     * @param  string  $key
     * @return void
     */
    public function forget($key)
    {
        $cacheForgetProcedure = $this->config->get(
            'laravel-voltdb::default.cache.procedure.forget', $this->cacheForgetProcedure);
        $this->connection->procedure($cacheForgetProcedure, [$this->prefix.$key]);
    }

    /**
     * Remove all items from the cache.
     * @return void
     */
    public function flush()
    {
        $cacheFlushProcedure = $this->config->get(
            'laravel-voltdb::default.cache.procedure.flushAll', $this->cacheFlushProcedure);
        $this->connection->procedure($cacheFlushProcedure);
    }

    /**
     * Get the encrypter instance.
     *
     * @return \Illuminate\Encryption\Encrypter
     */
    public function getEncrypter()
    {
        return $this->encrypter;
    }

    /**
     * Get the cache key prefix.
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

}
