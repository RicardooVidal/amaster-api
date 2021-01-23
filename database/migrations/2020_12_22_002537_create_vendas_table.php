<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVendasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vendas', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('valor_total');
            $table->bigInteger('desconto')->nullable();
            $table->bigInteger('troco')->nullable();
            $table->integer('tipo_pagamento_id');
            $table->date('data_venda');
            $table->string('observacao')->nullable();

            $table->foreign('tipo_pagamento_id')
                ->references('id')
                ->on('tipo_pagamento');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vendas');
    }
}
