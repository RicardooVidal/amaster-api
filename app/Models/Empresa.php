<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Empresa extends Model
{
    public $timestamps = true;
    protected $table = 'configuracao.empresa';
    protected $fillable = [
        'empresa', 'base', 'cnpj', 'expira', 'ativo'
    ];
}
