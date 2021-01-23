<?php

namespace App\Console\Commands;

use App\Models\User;
use Exception;
use Illuminate\Console\Command;

class CreateNewUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create-user {user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cria novo usuário';

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
            User::create([
                'id_empresa' => 0,
                'username' => $this->argument('user'),
                'nome' => 'NÃO INFORMADO',
                'password' => app('hash')->make('mudar@123')
            ]);
        } catch (Exception $e) {
            throw $e;
        }
    }
}