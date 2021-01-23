<?php

namespace App\Models;

use App\Util\Database;
use Elasticsearch\ClientBuilder;
use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
    public $timestamps = true;
    protected $fillable = ['codigo_barra', 'descricao', 'unidade_medida', 'categoria_id', 'ativo'];
    protected $perPage = 10;

    public static function decreaseEstoque($produto) 
    {
        $estoque = Estoque::find($produto['id']);
        $estoque->quantidade_atual -= $produto['quantidade'];
        $estoque->save();
    }

    public static function decreaseEstoqueElasticsearch($produtos)
    {
        foreach($produtos as $produto) {
            $data = [
                'index' => Database::getSchemaByConfig() . '.produtos',
                'id'    => $produto['id'],
                'body'  => [
                    'script' => [
                        'source' => 'ctx._source.quantidade_atual -= ' . $produto['quantidade']
                    ]

                ]
            ];

            $client = ClientBuilder::create()->build();
            return $client->update($data);
        }
    }
}
