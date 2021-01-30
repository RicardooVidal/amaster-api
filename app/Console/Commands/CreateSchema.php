<?php

namespace App\Console\Commands;

use App\Models\User;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class CreateSchema extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create-schema {schema}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cria novo schema';

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
     * @param $schema Nome do schema
     * @return mixed
     */
    public function handle()
    {
        try {
            DB::statement('CREATE SCHEMA ' . $this->argument('schema'));
            
            DB::statement(
                "CREATE TABLE " . $this->argument('schema') . ".migrations (
                    id SERIAL,
                    migration VARCHAR(255),
                    batch integer ,
                    PRIMARY KEY (id)
                 )"
            );

            $schema = $this->argument('schema');

            config(['database.migrations' => $schema]);

            config(['database.connections.' . env('DB_CONNECTION') . '.schema' => $schema]);

            DB::reconnect(env('DB_CONNECTION'));

            // Migrations
            Artisan::call('migrate', [
                '--path' => '/database/migrations/2014_10_12_000000_create_users_table.php',
                '--force' => true,
            ]);

            Artisan::call('migrate', [
                '--path' => '/database/migrations/2014_10_12_100000_create_password_resets_table.php',
                '--force' => true,
            ]);

            Artisan::call('migrate', [
                '--path' => '/database/migrations/2020_12_22_002536_tipo_pagamento.php',
                '--force' => true,
            ]);

            Artisan::call('migrate', [
                '--path' => '/database/migrations/2020_12_22_002537_create_vendas_table.php',
                '--force' => true,
            ]);

            Artisan::call('migrate', [
                '--path' => '/database/migrations/2020_12_22_002543_create_produtos_table.php',
                '--force' => true,
            ]);

            Artisan::call('migrate', [
                '--path' => '/database/migrations/2020_12_22_002545_create_vendas_produtos_table.php',
                '--force' => true,
            ]);

            Artisan::call('migrate', [
                '--path' => '/database/migrations/2020_12_22_002549_create_estoque_table.php',
                '--force' => true,
            ]);

            Artisan::call('migrate', [
                '--path' => '/database/migrations/2021_01_14_011616_create_categorias_table.php',
                '--force' => true,
            ]);

            // Seed
            Artisan::call('db:seed', [
                '--force' => true
            ]); 
        } catch (Exception $e) {
            throw $e;
        }
    }
}