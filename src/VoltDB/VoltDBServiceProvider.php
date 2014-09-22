<?php
namespace Ytake\LaravelVoltDB;

use VoltClient;
use Ytake\VoltDB\Parse;
use Illuminate\Support\ServiceProvider;
use Ytake\LaravelVoltDB\Cache\VoltDBStore;
use Ytake\LaravelVoltDB\Session\VoltDBSessionHandler;
use Ytake\VoltDB\Exception\MethodNotSupportedException;
use Illuminate\Support\Facades\Event AS IlluminateEvent;
use Ytake\LaravelVoltDB\Authenticate\VoltDBUserProvider;

/**
 * Class VoltDBServiceProvider
 * @package Ytake\LaravelVoltDB
 * @author yuuki.takezawa<yuuki.takezawa@comnect.jp.net>
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
        // required voltdb extension
        if (extension_loaded('voltdb')) {
            // register auth 'voltdb' driver
            $this->registerAuthenticate();
            // register cache 'voltdb' driver
            $this->registerCacheDriver();
            // register commands
            $this->registerCommands();
        }
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

        /**
         * required voltdb extension
         * @see https://github.com/VoltDB/voltdb-client-php/tree/native
         */
        if (extension_loaded('voltdb')) {
            // add voltdb extension
            $this->app['db']->extend(
                'voltdb',
                function ($config) {
                    //
                    return new ClientConnection(
                        new \Ytake\VoltDB\Client(new VoltClient, new Parse), $config
                    );
                }
            );

            // add voltdb session driver
            $this->app['session']->extend(
                'voltdb',
                function ($app) {
                    // for session
                    $default = $app['config']->get('laravel-voltdb::default.session.database', $this->default);
                    return new VoltDBSessionHandler(
                        $app['db']->connection($default),
                        $app['config']
                    );
                }
            );

            // event listen
            IlluminateEvent::listen('voltdb.not_supported', function($function) {
                    // throw Exception
                    throw new MethodNotSupportedException("{$function} is not supported.", 500);
                }
            );
        }
        // json interface API
        $this->app->bindShared('voltdb-api', function($app) {
                return new HttpClient($app['config'], new Parse);
            }
        );

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
            'session',
            'command.voltdb.info',
            'command.voltdb.schema.publish',
            'command.voltdb.system.catalog'
        ];
    }

    /**
     * register 'voltdb' authenticate driver
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
            }
        );
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
                        $app['config']
                    )
                );
            }
        );
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
            }
        );
        $this->app['command.voltdb.schema.publish'] = $this->app->share(function($app) {
                return new \Ytake\LaravelVoltDB\Console\SchemaPublishCommand($app['files']);
            }
        );
        $this->app['command.voltdb.system.catalog'] = $this->app->share(function($app) {
                // for system catalog database
                $default = $app['config']->get('laravel-voltdb::default.system.database', $this->default);
                return new \Ytake\LaravelVoltDB\Console\SystemCatalogCommand($app['db'], $default);
            }
        );
        $this->commands([
                'command.voltdb.info',
                'command.voltdb.schema.publish',
                'command.voltdb.system.catalog'
            ]
        );
    }

}