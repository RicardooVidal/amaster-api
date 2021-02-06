<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venda extends Model
{
    public $timestamps = false;
    protected $fillable = ['valor_total', 'tipo_pagamento_id', 'desconto', 'troco', 'data_venda', 'observacao'];
    protected $perPage = 20;
}
