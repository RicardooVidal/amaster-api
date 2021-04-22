<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendaProduto extends Model
{
    protected $table = 'vendas_produtos';
    public $timestamps = false;
    protected $fillable = ['id_venda', 'id_produto', 'quantidade', 'preco', 'preco_custo', 'valor_total'];

    public function venda()
    {
        return $this->belongsTo(Venda::class);
    }
}
