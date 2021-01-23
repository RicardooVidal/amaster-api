<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Models\Venda;
use App\Models\VendaProduto;
use App\Models\TipoPagamento;
use App\Traits\ElasticsearchClientTrait;
use App\Util\Database;
use Carbon\Carbon;
use Illuminate\Http\Request;

class VendaController extends Controller
{
    use ElasticsearchClientTrait;

    public function index(Request $request)
    {
        $offset = ($request->page - 1) * $request->per_page;
    
        $vendas = Venda::orderBy('vendas.id')
            ->paginate($request->per_page);

        return $vendas;
    }

    public function getVendasPendentes(Request $request) 
    {
        $offset = ($request->page - 1) * $request->per_page;

        $vendas = Venda::where('vendas.tipo_pagamento_id', '6')
            ->orderBy('vendas.id')
            ->paginate($request->per_page);

        return $vendas;
    }

    public function store(Request $request)
    {   
        try {
            $venda = Venda::create([
                'valor_total' => $request->valor_total,
                'tipo_pagamento_id' => $request->tipo_pagamento_id,
                'desconto' => $request->desconto,
                'troco' => $request->troco,
                'observacao' => $request->observacao,
                'data_venda' => Carbon::now()->toDateString()
            ]);
    
            if ($venda) {
                foreach($request->produtos as $produto) {
                    VendaProduto::create([
                        'id_venda' => $venda->id,
                        'id_produto' => $produto['id'],
                        'quantidade' => $produto['quantidade'],
                        'preco' => $produto['preco'],
                        'valor_total' => $produto['preco'] * $produto['quantidade']
                    ]);
                    // tira a quantidade vendida do estoque na tabela de produto
                    Produto::decreaseEstoque($produto);
                }
            }

            $this->createElasticsearch($request, $venda->id);
    
            return response()
            ->json(
                [
                    'msg' => 'Venda finalizada com sucesso.',
                    'status' => 'ok'
                ]
                , 201
            );
        } catch (\Exception $e) {
            return response()
            ->json(
                [
                    'msg' => $e->getMessage(),
                    'status' => 'error'
                ]
                , 500
            );
        }
    }

    public function update(Request $request)
    {
        try {
            $recurso = Venda::find($request->id);

            $recurso->fill([
                'tipo_pagamento_id' => $request->tipo_pagamento_id,
                'troco' => $request->troco,
            ]);
    
            $recurso->save();
    
            $this->updateElasticsearch($request, $request->id);
            
            return response()
            ->json(
                [
                    'msg' => 'Venda atualizada com sucesso.',
                    'status' => 'ok'
                ]
                , 201
            );
        } catch (\Exception $e) {
            return response()
            ->json(
                [
                    'msg' => $e->getMessage(),
                    'status' => 'error'
                ]
                , 500
            );
        }
    }

    public function show(int $id)
    {
        $recurso = Venda::join('vendas_produtos', 'vendas_produtos.id_venda', '=', 'vendas.id')
        ->join('produtos', 'produtos.id', '=', 'vendas_produtos.id_produto')
        ->select(
            'vendas.id',
            'vendas.data_venda',
            'vendas.tipo_pagamento_id',
            'vendas_produtos.id_produto',
            'produtos.descricao',
            'vendas_produtos.quantidade', 
            'vendas_produtos.preco',
            'vendas_produtos.valor_total'
        )
        ->where('vendas.id', '=', $id)
        ->get();

        $venda['venda'] = $recurso;

        return response()->json($venda);
    }

    public function destroy(int $id)
    {
        try {
            $qtdRecursosRemovidos = Venda::destroy($id);
            $this->deleteElasticsearch($id);
            return response()
            ->json(
                [
                    'msg' => 'Venda removida com sucesso.',
                    'status' => 'ok'
                ]
                , 200
            );
        } catch (\Exception $e) {
            return response()
            ->json(
                [
                    'msg' => $e->getMessage(),
                    'status' => 'error'
                ]
                , 500
            );
        }
    }

    public function createElasticsearch($data, $id)
    {
        $tipo_pagamento = TipoPagamento::find($data->tipo_pagamento_id);
        $produtos = null;

        foreach($data->produtos as $produto) {
            $produtos[] = [
                'id' => $produto['id'],
                'descricao' => $produto['descricao'],
                'quantidade' => intval($produto['quantidade']),
                'preco' => intval($produto['preco']),
                'categoria' => $produto['categoria']
            ];
        }

        $data = [
            'body' => [
                'tipo_pagamento'   => $tipo_pagamento->descricao,
                'desconto'         => intval($data->desconto),
                'troco'            => intval($data->troco),
                'observacao'       => $data->observacao,
                'valor_total'      => intval($data->valor_total),
                'data_venda'       => Carbon::now()->toDateString(),
                'produtos'         => $produtos,
            ],
            'id' => $id,
            'index' => Database::getSchemaByConfig() . '.vendas',
        ];  

        $this->esIndex($data);

        // tira a quantidade vendida do estoque no elasticsearch
        Produto::decreaseEstoqueElasticsearch($produtos);
    }

    public function updateElasticsearch($data, $id)
    {
        $tipo_pagamento = TipoPagamento::find($data->tipo_pagamento_id);

        $data = [
            'index' => Database::getSchemaByConfig() . '.vendas',
            'id'    => $id,
            'body'  => [
                'doc' => [
                    'data_venda'       => Carbon::now()->toDateString(),
                    'tipo_pagamento'   => $tipo_pagamento->descricao,
                    'troco'            => intval($data->troco)
                ]
            ]
        ];
        
        return $this->esUpdate($data);
    }

    public function deleteElasticsearch(int $id)
    {
        $params = [
            'index' => Database::getSchemaByConfig() . '.vendas',
            'id'    => $id
        ];

        return $this->esDelete($params);
    }

    public function searchExact(Request $request)
    {
        $field = $request->field;
        $string = $request->string;
        
        $params = [
            'index' => Database::getSchemaByConfig() . '.vendas',
            'body'  => [
                'query' => [
                    'match' => [
                        $field  => $string
                    ]
                ]
            ]
        ];

        return $this->esSearch($params);
    }
}