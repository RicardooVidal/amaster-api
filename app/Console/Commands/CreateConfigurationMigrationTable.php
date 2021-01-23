<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateConfigurationMigrationTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create-config-migration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cria tabela de migration para o schema "configuracao"';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            DB::statement(
                "CREATE TABLE " ."configuracao.migrations (
                    id SERIAL,
                    migration VARCHAR(255),
                    batch integer ,
                    PRIMARY KEY (id)
                 )"
            );
        } catch (Exception $e) {
            throw $e;
        }
    }
}