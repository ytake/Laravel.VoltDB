<?php
namespace Ytake\LaravelVoltDB\Authenticate;

use Illuminate\Auth\UserInterface;
use Illuminate\Config\Repository;

/**
 * Class VoltDBUser
 * @package Ytake\LaravelVoltDB\Authenticate
 * @author yuuki.takezawa<yuuki.takezawa@comnect.jp.net>
 * @license http://opensource.org/licenses/MIT MIT
 */
class VoltDBUser implements UserInterface
{

    /**
     * All of the user's attributes.
     * @var array
     */
    protected $attributes;

    /** @var \Illuminate\Config\Repository  */
    protected $config;

    /** @var string  */
    protected $id;

    /** @var string  */
    protected $password;

    /** @var string */
    protected $token;

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes, Repository $config)
    {
        $this->attributes = $attributes;
        $this->config = $config;
        $this->id = $config->get('laravel-voltdb::default.auth.column_name.id', 'id');
        $this->password = $config->get('laravel-voltdb::default.auth.column_name.password', 'password');
        $this->token = $config->get('laravel-voltdb::default.auth.column_name.remember_token', 'remember_token');
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->attributes[$this->id];
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->attributes[$this->password];
    }

    /**
     * Get the token value for the "remember me" session.
     *
     * @return string
     */
    public function getRememberToken()
    {
        return $this->attributes[$this->token];
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param  string  $value
     * @return void
     */
    public function setRememberToken($value)
    {
        $this->attributes[$this->token] = $value;
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName()
    {
        return $this->token;
    }

    /**
     * Dynamically access the user's attributes.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->attributes[$key];
    }

    /**
     * Dynamically set an attribute on the user.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return void
     */
    public function __set($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    /**
     * Dynamically check if a value is set on the user.
     *
     * @param  string  $key
     * @return bool
     */
    public function __isset($key)
    {
        return isset($this->attributes[$key]);
    }

    /**
     * Dynamically unset a value on the user.
     *
     * @param  string  $key
     * @return bool
     */
    public function __unset($key)
    {
        unset($this->attributes[$key]);
    }

}
