<?php
namespace Ytake\LaravelVoltDB;

use VoltClient;
use Ytake\LaravelVoltDB\Cache\VoltDBStore;
use Ytake\VoltDB\Parse;
use Ytake\LaravelVoltDB\Client;
use Ytake\LaravelVoltDB\HttpClient;
use Illuminate\Support\ServiceProvider;
use Ytake\VoltDB\Exception\MethodNotSupportedException;
use Illuminate\Support\Facades\Event AS IlluminateEvent;
use Ytake\LaravelVoltDB\Authenticate\VoltDBUserProvider;

/**
 * Class VoltDBServiceProvider
 *
 * @package Ytake\LaravelVoltDB
 * @author  yuuki.takezawa<yuuki.takezawa@excite.jp>
 * @license http://opensource.org/licenses/MIT MIT
 */
class VoltDBServiceProvider extends ServiceProvider
{

    /** @var string default database name */
    protected $default = 'voltdb';

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the applicat0ion events.
     * @return void
     */
    public function boot()
    {
        $this->package('ytake/laravel-voltdb');
        // register auth 'voltdb' driver
        $this->registerAuthenticate();
        // register cache 'voltdb' driver
        $this->registerCacheDriver();
        // register commands
        $this->registerCommands();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // voltdb api access(json interface) configure
        $this->app['config']->package('ytake/laravel-voltdb', __DIR__ . '/../config');

        // add voltdb extension
        $this->app['db']->extend('voltdb', function($config) {
            //
            return new Client(new \Ytake\VoltDB\Client(new VoltClient, new Parse), $config);
        });

        // json interface API
        $this->app->bindShared('voltdb-api', function($app) {
            return new HttpClient($app['config'], new Parse);
        });

        // event listen
        IlluminateEvent::listen('voltdb.not_supported', function($function) {
            // throw Exception
            throw new MethodNotSupportedException("{$function} is not supported.", 500);
        });
    }

    /**
     * Get the services provided by the provider.
     * @return array
     */
    public function provides()
    {
        return [
            'voltdb-api',
            'auth',
            'cache',
            'command.voltdb.info',
            'command.voltdb.auth.publish',
            'command.voltdb.system.catalog'
        ];
    }

    /**
     * @return void
     */
    protected function registerAuthenticate()
    {
        $this->app['auth']->extend('voltdb', function($app) {
            //
            $default = $app['config']->get('laravel-voltdb::default.auth.database', $this->default);
            return new \Illuminate\Auth\Guard(
                new VoltDBUserProvider(
                    $app['db']->connection($default),
                    $app['hash'],
                    $app['config']
                ), $app['session.store']
            );
        });
    }

    /**
     * register 'voltdb' cache driver
     * @return void
     */
    protected function registerCacheDriver()
    {
        $this->app['cache']->extend('voltdb', function($app) {
            // for cache
            $default = $app['config']->get('laravel-voltdb::default.cache.database', $this->default);
            return new \Illuminate\Cache\Repository(
                new VoltDBStore(
                    $app['db']->connection($default),
                    $app['encrypter'],
                    $app['config'],
                    $default
                )
            );
        });
    }
    /**
     * register artisan command
     * @return void
     */
    protected function registerCommands()
    {
        //
        $this->app['command.voltdb.info'] = $this->app->share(function($app) {
                return new \Ytake\LaravelVoltDB\Console\InformationCommand;
        });
        $this->commands('command.voltdb.info');
        //
        $this->app['command.voltdb.auth.publish'] = $this->app->share(function($app) {
            return new \Ytake\LaravelVoltDB\Console\SchemaPublishCommand;
        });
        $this->commands('command.voltdb.auth.publish');
        //
        $this->app['command.voltdb.system.catalog'] = $this->app->share(function($app) {
            // for system catalog database
            $default = $app['config']->get('laravel-voltdb::default.system.database', $this->default);
            return new \Ytake\LaravelVoltDB\Console\SystemCatalogCommand($app['db'], $default);
        });
        $this->commands('command.voltdb.system.catalog');
    }

}