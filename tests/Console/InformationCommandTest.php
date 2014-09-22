<?php

class InformationCommandTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Ytake\LaravelVoltDB\Console\InformationCommand  */
    protected $command;
    public function setUp()
    {
        parent::setUp();
        $this->command = new \Ytake\LaravelVoltDB\Console\InformationCommand;
    }

    public function testInstance()
    {
        $this->assertInstanceOf("Ytake\LaravelVoltDB\Console\InformationCommand", $this->command);
    }

    public function testCommand()
    {
        $this->command->run(
            new \Symfony\Component\Console\Input\ArrayInput([]),
            new \Symfony\Component\Console\Output\NullOutput
        );
        $this->assertInstanceOf("Symfony\Component\Console\Output\NullOutput", $this->command->getOutput());
        $this->assertSame("information about ytake/laravel-voltdb", $this->command->getDescription());
        $this->assertSame("ytake:voltdb-info", $this->command->getSynopsis());
    }
} 