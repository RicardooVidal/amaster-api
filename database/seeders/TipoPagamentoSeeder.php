<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use \App\Models\TipoPagamento;

class TipoPagamentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TipoPagamento::create(['descricao' => 'DINHEIRO']);
        TipoPagamento::create(['descricao' => 'CARTÃO CRÉDITO']);
        TipoPagamento::create(['descricao' => 'CARTÃO DÉBITO']);
        TipoPagamento::create(['descricao' => 'CHEQUE']);
        TipoPagamento::create(['descricao' => 'OUTROS']);
        TipoPagamento::create(['descricao' => 'PENDENTE']);
    }
}
