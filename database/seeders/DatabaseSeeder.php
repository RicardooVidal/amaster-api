<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use TipoPagamento;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(CategoriasSeeder::class);
        $this->command->info('Seed Categorias Ok');

        $this->call(TipoPagamentoSeeder::class);
        $this->command->info('Seed Tipo Pagamento Ok');
    }
}
