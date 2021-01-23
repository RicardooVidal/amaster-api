<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEstoqueTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('estoque', function (Blueprint $table) {
            $table->integer('id_produto');
            $table->integer('quantidade_atual')->nullable();
            $table->date('ultima_entrada')->nullable();
            $table->bigInteger('preco')->nullable();
            $table->bigInteger('preco_custo')->nullable();
            $table->timestamps();


            $table->foreign('id_produto')
                ->references('id')
                ->on('produtos')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('estoque');
    }
}
