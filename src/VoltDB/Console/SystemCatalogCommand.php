<?php
namespace Ytake\LaravelVoltDB\Console;

use Illuminate\Console\Command;
use Illuminate\Database\DatabaseManager;
use Symfony\Component\Console\Input\InputOption;

/**
 * renderer stored procedure SystemCatalog
 *
 * Class SystemCatalogCommand
 * @package Ytake\LaravelVoltDB\Console
 * @author yuuki.takezawa<yuuki.takezawa@comnect.jp.net>
 * @license http://opensource.org/licenses/MIT MIT
 * @see https://voltdb.com/docs/UsingVoltDB/sysprocsystemcatalog.php
 */
class SystemCatalogCommand extends Command
{

    /**
     * The console command name.
     * @var string
     */
    protected $name = 'ytake:voltdb-system-catalog';

    /**
     * The console command description.
     * @var string
     */
    protected $description = "render system catalog";

    /** @var DatabaseManager */
    protected $manager;

    /** @var string  */
    protected $database;

    /** @var array  */
    protected $tableHeader = [
        "TABLE_NAME",
        "TABLE_TYPE"
    ];

    /** @var array  */
    protected $columnHeader = [
        "TABLE_NAME",
        "COLUMN_NAME",
        "DATA_TYPE",
        "TYPE_NAME",
        "COLUMN_SIZE",
        "DECIMAL_DIGITS",
        "NUM_PREC_RADIX",
        "NULLABLE",
        "REMARKS",
        "COLUMN_DEF",
        "CHAR_OCTET_LENGTH",
        "ORDINAL_POSITION",
        "IS_NULLABLE",
        "IS_AUTOINCREMENT",
    ];

    /** @var array  */
    protected $indexHeader = [
        "TABLE_NAME",
        "NON_UNIQUE",
        "INDEX_NAME",
        "TYPE",
        "ORDINAL_POSITION",
        "COLUMN_NAME",
        "ASC_OR_DESC"
    ];

    /** @var array  */
    protected $primaryHeader = [
        "TABLE_NAME",
        "COLUMN_NAME",
        "KEY_SEQ",
        "PK_NAME",
    ];

    /** @var array  */
    protected $procedureHeader = [
        "PROCEDURE_NAME",
        "PROCEDURE_TYPE",
        "SPECIFIC_NAME",
    ];

    /** @var array  */
    protected $procedureColumnHeader = [
        "PROCEDURE_NAME",
        "COLUMN_NAME",
        "COLUMN_TYPE",
        "DATA_TYPE",
        "TYPE_NAME",
        "PRECISION",
        "LENGTH",
        "SCALE",
        "RADIX",
        "REMARKS",
        "CHAR_OCTET_LENGTH",
        "ORDINAL_POSITION",
        "SPECIFIC_NAME"
    ];

    /**
     * @param DatabaseManager $manager
     * @param string $database connect Database
     */
    public function __construct(DatabaseManager $manager, $database = 'voltdb')
    {
        parent::__construct();
        $this->manager = $manager;
        $this->database = $database;
    }

    /**
     * Get the console command options.
     * @return array
     */
    protected function getOptions()
    {
        return [
            [
                'component',
                'c',
                InputOption::VALUE_OPTIONAL,
                'returns information about the schema of the VoltDB database, depending upon the component keyword you specify.'
            ],
        ];
    }

    /**
     * Execute the console command.
     * @return void
     */
    public function fire()
    {
        if(is_null($this->option('component'))) {
            $this->getTableRenderer("TABLES", $this->tableHeader);
            $this->getTableRenderer("COLUMNS", $this->columnHeader);
            $this->getTableRenderer("INDEXINFO", $this->indexHeader);
            $this->getTableRenderer("PRIMARYKEYS", $this->primaryHeader);
            $this->getTableRenderer("PROCEDURES", $this->procedureHeader);
            $this->getTableRenderer("PROCEDURECOLUMNS", $this->procedureColumnHeader);

        } else {
            $component = strtoupper($this->option('component'));
            if ("TABLES" == $component) {
                $this->getTableRenderer("TABLES", $this->tableHeader);
            }
            if ("COLUMNS" == $component) {
                $this->getTableRenderer("COLUMNS", $this->columnHeader);
            }
            if ("INDEXINFO" == $component) {
                $this->getTableRenderer("INDEXINFO", $this->indexHeader);
            }
            if ("PRIMARYKEYS" == $component) {
                $this->getTableRenderer("PRIMARYKEYS", $this->primaryHeader);
            }
            if("PROCEDURES" == $component) {
                $this->getTableRenderer("PROCEDURES", $this->procedureHeader);
            }
            if("PROCEDURECOLUMNS" == $component) {
                $this->getTableRenderer("PROCEDURECOLUMNS", $this->procedureColumnHeader);
            }
        }
    }

    /**
     * table renderer
     * @access private
     * @param $component
     * @param $header
     * @return void
     */
    private function getTableRenderer($component, $header)
    {
        $result = [];
        $systemCatalog = $this->manager->connection($this->database)
            ->procedure("@SystemCatalog", [$component]);
        if($systemCatalog) {
            $i = 0;
            foreach($systemCatalog as $row) {
                foreach($header as $head) {
                    $result[$i][] = $row[$head];
                }
                $i++;
            }
        }
        $this->line("<comment>SystemCatalog / {$component}</comment>");
        $this->table($header, $result);
    }
}

