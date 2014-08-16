<?php

class SchemaCommandTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Ytake\LaravelVoltDB\Console\SchemaPublishCommand  */
    protected $command;

    public function setUp()
    {
        parent::setUp();
        $this->command = new \Ytake\LaravelVoltDB\Console\SchemaPublishCommand;
    }

    public function testInstance()
    {
        $this->assertInstanceOf("Ytake\LaravelVoltDB\Console\SchemaPublishCommand", $this->command);
    }

    public function testCommand()
    {
        $this->assertSame("publish DDL for Laravel.VoltDB package", $this->command->getDescription());
        $this->assertSame("ytake:voltdb-schema-publish", $this->command->getName());
    }
} 