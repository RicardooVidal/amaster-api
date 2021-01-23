<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Estoque;
use App\Models\Produto;
use App\Util\Money;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Traits\ElasticsearchClientTrait;
use App\Util\Database;

class ProdutoController extends Controller
{
    use ElasticsearchClientTrait;

    public function index(Request $request)
    {
        $offset = ($request->page - 1) * $request->per_page;
        $produtos = Produto::join('estoque', 'estoque.id_produto', '=', 'produtos.id')
            ->orderBy('produtos.id')
            ->paginate($request->per_page);

        return $produtos;
    }

    public function store(Request $request)
    {
        $produto = Produto::create([
            'codigo_barra' => $request->codigo_barra,
            'descricao' => $request->descricao,
            'unidade_medida' => $request->unidade_medida,
            'categoria_id' => $request->categoria_id,
            'ativo' => $request->ativo
        ]); 

        if ($produto) {
            $estoque = Estoque::create([
                'id_produto'       => $produto->id,
                'quantidade_atual' => $request->quantidade_estoque,
                'ultima_entrada'   => Carbon::now()->toDateString(),
                'preco_custo'      => Money::parseInt($request->preco_custo),
                'preco'            => Money::parseInt($request->preco_venda)
            ]);
        }

        if ($estoque) {
            $this->createOrUpdateElasticsearch($request, $produto->id);
            return response()
            ->json(
                [
                    'id' => $produto->id,
                    'msg' => 'Processo realizado com sucesso.',
                    'status' => 'ok'
                ],
                201
            );
        }

        return response()
        ->json(
            [
                'msg' => 'Houve um problema para processar a requisição',
                'status' => 'error'
            ]
            , 501
        );

    }

    public function show(int $id)
    {
        $produto = Produto::find($id);
        $estoque = Estoque::find($id);
        return response()->json([
            'produto' => $produto,
            'estoque' => $estoque
        ]);
    }

    public function update(Request $request)
    {
        $recurso = Produto::find($request->id);

        $recurso->fill([
            'codigo_barra' => $request->codigo_barra,
            'descricao' => $request->descricao,
            'unidade_medida' => $request->unidade_medida,
            'categoria_id' => $request->categoria_id,
            'ativo' => $request->ativo
        ]);
        $produto = $recurso->save();

        if ($produto) {
            $recurso = Estoque::find($request->id);

            $recurso->fill([
                'quantidade_atual' => $request->quantidade_estoque,
                'ultima_entrada'   => Carbon::now()->toDateString(),
                'preco_custo'      => Money::parseInt($request->preco_custo),
                'preco'            => Money::parseInt($request->preco_venda)
            ]);
            $estoque = $recurso->save();
        }

        if ($estoque) {
            $this->createOrUpdateElasticsearch($request, $request->id);
            return response()
            ->json(
                [
                    'id' => $request->id,
                    'msg' => 'Processo realizado com sucesso.',
                    'status' => 'ok'
                ],
                201
            );
        }

        return response()
        ->json(
            [
                'msg' => 'Houve um problema para processar a requisição.',
                'status' => 'error'
            ]
            , 500
        );
    }

    public function destroy(int $id)
    {
        $qtdRecursosRemovidos = Produto::destroy($id);
        $this->deleteElasticsearch($id);
        if ($qtdRecursosRemovidos) {
            return response()
            ->json(
                [
                    'msg' => 'Produto removido com sucesso.',
                    'status' => 'ok'
                ]
                , 200
            );
        }

        return response()
        ->json(
            [
                'msg' => 'Não foi possível excluir o produto. Verifique se o mesmo não possui vendas.',
                'status' => 'error'
            ]
            , 500
        );
    }

    public function createOrUpdateElasticsearch($data, $id)
    {
        $categoria = Categoria::find($data->categoria_id);

        $data = [
            'body' => [
                'codigo_barra'     => $data->codigo_barra,
                'descricao'        => $data->descricao,
                'unidade_medida'   => $data->unidade_medida,
                'quantidade_atual' => intval($data->quantidade_estoque),
                'ultima_entrada'   => Carbon::now()->toDateString(),
                'categoria'        => $categoria->descricao,
                'preco_custo'      => Money::parseInt($data->preco_custo),
                'preco'            => Money::parseInt($data->preco_venda),
                'ativo'            => boolval(intval($data->ativo)),
            ],
            'id' => $id,
            'index' => Database::getSchemaByConfig() . '.produtos',
        ];

        $this->esIndex($data);
    }

    public function deleteElasticsearch(int $id)
    {
        $params = [
            'index' => Database::getSchemaByConfig() . 'produtos',
            'id'    => $id
        ];

        $this->esDelete($params);
    }

    public function searchByField(Request $request)
    {
        $field = $request->field;
        $string = $request->string;    
        $params = [
            'index' => Database::getSchemaByConfig() . '.produtos',
            'body'  => [
                'query' => [
                    'wildcard' => [
                        $field  => $string . '*'
                    ]
                ]
            ]
        ];

        return $this->esSearch($params);
    }

    public function searchExact(Request $request)
    {
        $field = $request->field;
        $string = $request->string;
        
        $params = [
            'index' => Database::getSchemaByConfig() . '.produtos',
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