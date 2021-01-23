<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estoque extends Model
{
    protected $table = 'estoque';
    protected $primaryKey = 'id_produto';
    public $timestamps = true;
    protected $fillable = ['id_produto','quantidade_atual', 'ultima_entrada', 'preco', 'preco_custo'];
    protected $perPage = 3;

    public function produto()
    {
        return $this->belongsTo(Produto::class);
    }
}
