<?php
use Illuminate\Filesystem\Filesystem;

class SchemaCommandTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Ytake\LaravelVoltDB\Console\SchemaPublishCommand  */
    protected $command;
    /** @var \Illuminate\Filesystem\Filesystem  */
    protected $file;
    public function setUp()
    {
        parent::setUp();
        $this->file = new Filesystem;
        $this->command = new \Ytake\LaravelVoltDB\Console\SchemaPublishCommand(
            $this->file
        );
    }

    public function testInstance()
    {
        $this->assertInstanceOf("Ytake\LaravelVoltDB\Console\SchemaPublishCommand", $this->command);
    }

    public function testCommand()
    {
        $this->command->run(
            new \Symfony\Component\Console\Input\ArrayInput(
                [
                    '--publish' => PATH . '/schema'
                ]
            ),
            new \Symfony\Component\Console\Output\NullOutput
        );
        $this->assertInstanceOf("Symfony\Component\Console\Output\NullOutput", $this->command->getOutput());
        $this->assertSame("publish DDL for Laravel.VoltDB package", $this->command->getDescription());
        $this->assertSame("ytake:voltdb-schema-publish", $this->command->getName());
        $this->assertSame(true, $this->file->exists(PATH . '/schema/ddl.sql'));
    }

} 