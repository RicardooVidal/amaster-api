<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVendasProdutosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vendas_produtos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_venda');
            $table->integer('id_produto');
            $table->integer('quantidade');
            $table->bigInteger('preco')->nullable();
            $table->bigInteger('valor_total')->nullable();

            $table->foreign('id_venda')
                ->references('id')
                ->on('vendas')
                ->onDelete('cascade');

            $table->foreign('id_produto')
            ->references('id')
            ->on('produtos');
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
