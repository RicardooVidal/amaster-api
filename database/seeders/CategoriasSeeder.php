<?php

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Categoria::create(['descricao' => 'BEBIDAS']);
        Categoria::create(['descricao' => 'SALGADOS']);
        Categoria::create(['descricao' => 'DOCES']);
        Categoria::create(['descricao' => 'OUTROS']);
    }
}
