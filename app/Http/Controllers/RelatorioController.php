<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Traits\ElasticsearchClientTrait;
use App\Util\Database;
use Illuminate\Http\Request;

class RelatorioController extends Controller
{
    use ElasticsearchClientTrait;

    public function byCategoria(Request $request)
    {
        // SELECT vendas.data_venda AS data_venda,
		// produtos.descricao AS produto,
		// categorias.descricao AS categoria,
		// SUM(vendas_produtos.quantidade) AS quantidade_total,
		// SUM(vendas_produtos.valor_total) AS total
        //     FROM produtos
        //     JOIN categorias ON categorias.id = produtos.categoria_id
        //     JOIN vendas_produtos ON vendas_produtos.id_produto = produtos.id
        //     JOIN vendas ON vendas.id = vendas_produtos.id_venda
        //     WHERE produtos.categoria_id = 3
        //     GROUP BY vendas.data_venda, produtos.descricao, categorias.descricao
	
        $vendas = Produto::join('categorias', 'categorias.id', '=', 'produtos.categoria_id')
            ->join('vendas_produtos', 'vendas_produtos.id_produto', '=', 'produtos.id')
            ->join('vendas', 'vendas.id', '=', 'vendas_produtos.id_venda')
            ->selectRaw("vendas.data_venda AS data_venda, produtos.id AS produto_id, produtos.descricao AS produto, categorias.descricao AS categoria, 
                sum(vendas_produtos.quantidade) AS quantidade_total, 
                sum(vendas_produtos.valor_total) AS total"
            )
            ->where('produtos.categoria_id', '=', $request->categoria_id)
            ->where('data_venda', '>=', $request->data_inicial)
            ->where('data_venda', '<=', $request->data_final)
            ->groupBy('vendas.data_venda', 'produtos.id', 'produtos.descricao', 'categorias.descricao')
            ->get();

        $data['produtos'] = $vendas;

        return $data;
    }

    public function byPeriodo(Request $request)
    {
        $params = [
            'index' => Database::getSchemaByConfig() . '.vendas',
            'body' => [
                'query' => [
                    'bool' => [
                        'filter' => [
                            'range' => [
                                'data_venda' => [
                                    'gte' => $request->data_inicial,
                                    'lte' => $request->data_final,
                                    'format' => 'yyyy-MM-dd'
                                ] 
                            ]
                        ]
                    ]
                ]
            ]
        ];

        return $this->esSearch($params);
    }

    public function byTipoPagamento(Request $request)
    {
        $params = [
            'index' => Database::getSchemaByConfig() . '.vendas',
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => [
                            'match' => [
                                'tipo_pagamento' => $request->tipo_pagamento
                            ]
                        ],
                        'filter' => [
                            'range' => [
                                'data_venda' => [
                                    'gte' => $request->data_inicial,
                                    'lte' => $request->data_final,
                                    'format' => 'yyyy-MM-dd'
                                ] 
                            ]
                        ]
                    ]
                ]
            ]
        ];
        return $this->esSearch($params);
    }

    public function maisVendido(Request $request)
    {
        $vendas = Produto::join('categorias', 'categorias.id', '=', 'produtos.categoria_id')
            ->join('vendas_produtos', 'vendas_produtos.id_produto', '=', 'produtos.id')
            ->join('vendas', 'vendas.id', '=', 'vendas_produtos.id_venda')
            ->selectRaw("produtos.id AS produto_id, produtos.descricao AS produto, produtos.unidade_medida AS un, categorias.descricao AS categoria, 
                sum(vendas_produtos.quantidade) AS quantidade_total, 
                sum(vendas_produtos.valor_total) AS total"
            )
            ->where('data_venda', '>=', $request->data_inicial)
            ->where('data_venda', '<=', $request->data_final)
            ->groupBy('produtos.id', 'produtos.descricao', 'produtos.unidade_medida', 'categorias.descricao')
            ->orderBy('quantidade_total', 'DESC')
            ->limit(1)
            ->get();

        $data['produtos'] = $vendas;

        return $data;
    }
}