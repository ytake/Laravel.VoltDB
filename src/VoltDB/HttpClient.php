<?php
namespace Ytake\LaravelVoltDB;

use Ytake\VoltDB\Parse;
use Illuminate\Config\Repository;
use Ytake\VoltDB\HttpClient as BaseClient;

/**
 * Class HttpClient
 * @package Ytake\LaravelVoltDB
 * @author  yuuki.takezawa<yuuki.takezawa@comnect.jp.net>
 * @license http://opensource.org/licenses/MIT MIT
 */
class HttpClient extends BaseClient
{

    /**
     * @param Repository $repository
     * @param Parse $parse
     */
    public function __construct(Repository $repository, Parse $parse)
    {
        parent::__construct($parse);
        $this->setConfigure($repository);
    }

    /**
     * set configure
     * @param Repository $repository
     * @return void
     */
    protected function setConfigure(Repository $repository)
    {
        $this->host = $repository->get('ytake-laravel-voltdb.host', 'localhost');
        $this->path = $repository->get('ytake-laravel-voltdb.path', '/api/1.0/');
        $this->apiPort = $repository->get('ytake-laravel-voltdb.apiPort', 8080);
        $this->ssl = $repository->get('ytake-laravel-voltdb.ssl', false);
    }
} 
