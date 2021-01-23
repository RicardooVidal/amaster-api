<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoPagamento extends Model
{
    protected $table = 'tipo_pagamento';
    public $timestamps = false;
    protected $fillable = ['descricao'];
}
