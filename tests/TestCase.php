<?php

class TestCase extends \PHPUnit_Framework_TestCase
{

    /** @var \Illuminate\Config\Repository $config */
    protected $config;
    protected function setUp()
    {
        $this->config = new \Illuminate\Config\Repository();
        $filesystem = new \Illuminate\Filesystem\Filesystem;
        $items = $filesystem->getRequire(__DIR__ . '/config/ytake-laravel-voltdb.php');
        $this->config->set("ytake-laravel-voltdb", $items);
    }
    /**
     * @param $class
     * @param $name
     * @return \ReflectionMethod
     */
    protected function getProtectMethod($class, $name)
    {
        $class = new \ReflectionClass($class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }
    /**
     * @param $class
     * @param $name
     * @return \ReflectionProperty
     */
    protected function getProtectProperty($class, $name)
    {
        $class = new \ReflectionClass($class);
        $property = $class->getProperty($name);
        $property->setAccessible(true);
        return $property;
    }
}

class MockApplication extends \Illuminate\Container\Container implements \Illuminate\Contracts\Foundation\Application
{
    /**
     * Get the version number of the application.
     *
     * @return string
     */
    public function version()
    {
// TODO: Implement version() method.
    }
    /**
     * Get or check the current application environment.
     *
     * @param mixed
     * @return string
     */
    public function environment()
    {
// TODO: Implement environment() method.
    }
    /**
     * Determine if the application is currently down for maintenance.
     *
     * @return bool
     */
    public function isDownForMaintenance()
    {
// TODO: Implement isDownForMaintenance() method.
    }
    /**
     * Register all of the configured providers.
     *
     * @return void
     */
    public function registerConfiguredProviders()
    {
// TODO: Implement registerConfiguredProviders() method.
    }
    /**
     * Register a service provider with the application.
     *
     * @param \Illuminate\Support\ServiceProvider|string $provider
     * @param array $options
     * @param bool $force
     * @return \Illuminate\Support\ServiceProvider
     */
    public function register($provider, $options = [], $force = false)
    {
// TODO: Implement register() method.
    }
    /**
     * Register a deferred provider and service.
     *
     * @param string $provider
     * @param string $service
     * @return void
     */
    public function registerDeferredProvider($provider, $service = null)
    {
// TODO: Implement registerDeferredProvider() method.
    }
    /**
     * Boot the application's service providers.
     *
     * @return void
     */
    public function boot()
    {
// TODO: Implement boot() method.
    }
    /**
     * Register a new boot listener.
     *
     * @param mixed $callback
     * @return void
     */
    public function booting($callback)
    {
// TODO: Implement booting() method.
    }
    /**
     * Register a new "booted" listener.
     *
     * @param mixed $callback
     * @return void
     */
    public function booted($callback)
    {
// TODO: Implement booted() method.
    }
}
function base_path()
{
    return null;
}
function storage_path()
{
    return null;
}
