<?php
namespace Ytake\LaravelVoltDB;

use Illuminate\Support\Facades\Facade;

/**
 * Laravel's facade accessor(proxy)
 *
 * Class VoltDBFacade
 * @package Ytake\LaravelVoltDB
 * @author yuuki.takezawa<yuuki.takezawa@comnect.jp.net>
 * @license http://opensource.org/licenses/MIT MIT
 */
class VoltDBFacade extends Facade
{

    /**
     * Get the registered name of the component.
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'voltdb-api';
    }
}