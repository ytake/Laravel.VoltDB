<?php
namespace Ytake\LaravelVoltDB\Console;

use Illuminate\Console\Command;

/**
 * Class InformationCommand
 * @package Ytake\LaravelVoltDB\Console
 * @author yuuki.takezawa<yuuki.takezawa@comnect.jp.net>
 * @license http://opensource.org/licenses/MIT MIT
 */
class InformationCommand extends Command
{

    /**
     * The console command name.
     * @var string
     */
    protected $name = 'ytake:voltdb-info';

    /**
     * The console command description.
     * @var string
     */
    protected $description = "information about ytake/laravel-voltdb";

    protected $message = "
 _    __      ____  ____  ____
| |  / /___  / / /_/ __ \/ __ )
| | / / __ \/ / __/ / / / __  |
| |/ / /_/ / / /_/ /_/ / /_/ /
|___/\____/_/\__/_____/_____/ ";

    /**
     * Execute the console command.
     * @return void
     */
    public function fire()
    {
        $this->line("<info>{$this->message}</info>");
        $this->line('<info>VoltDB providers for Laravel </info>');
        $this->line('<comment>author:yuuki.takezawa<yuuki.takezawa@comnect.jp.net></comment>');
    }
}

