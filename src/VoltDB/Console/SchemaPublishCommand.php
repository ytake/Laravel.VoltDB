<?php
namespace Ytake\LaravelVoltDB\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class SchemaPublishCommand
 * @package Ytake\LaravelVoltDB\Console
 * @author yuuki.takezawa<yuuki.takezawa@comnect.jp.net>
 * @license http://opensource.org/licenses/MIT MIT
 */
class SchemaPublishCommand extends Command
{

    /**
     * The console command name.
     * @var string
     */
    protected $name = 'ytake:voltdb-schema-publish';

    /**
     * The console command description.
     * @var string
     */
    protected $description = "publish DDL for Laravel.VoltDB package";

    /**
     * @param Filesystem $file
     */
    public function __construct(Filesystem $file)
    {
        parent::__construct();
        $this->file = $file;
    }

    /**
     * Get the console command options.
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['publish', 'p', InputOption::VALUE_OPTIONAL, 'Publish to a specific directory'],
        ];
    }

    /**
     * Execute the console command.
     * @return void
     */
    public function fire()
    {
        $path = (is_null($this->option('publish'))) ? storage_path() . "/schema" : $this->option('publish');
        // clear all cache
        if(is_null($this->option('publish'))) {
            if(!$this->file->exists($path)) {
                $this->file->makeDirectory($path, 0777);
            }
        }
        $this->file->copy(__DIR__ . '/../schema/ddl.sql', $path . "/ddl.sql");

        $this->line("<info>published to {$path}/ddl.sql</info>");
        $this->line('<comment>merge your ddl and start voltdb</comment>');
        $this->line("<comment>example). $ voltdb compile {$path}/ddl.sql</comment>");
        $this->line("<comment>example). $ voltdb create catalog.jar</comment>");
    }
}