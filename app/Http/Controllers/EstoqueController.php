<?php

namespace App\Http\Controllers;

use App\Models\Estoque;
use Illuminate\Http\Request;

class EstoqueController extends Controller
{
    public function store(Request $request)
    {
        return response()
            ->json(
                // $this->classe::create($request->all()),
                Estoque::create([
                    'id_produto' => $request->id_produto,
                    'quantidade_atual' => $request->quantidade_atual,
                    'ultima_entrada' => $request->ultima_entrada,
                    'preco' => $request->preco,
                    'preco_custo' => $request->preco_custo
                ]),
                201
            );
    }

    public function show(int $id)
    {
        $recurso = Estoque::find($id);
        return response()->json($recurso);
    }

    public function update(int $id, Request $request)
    {
        $recurso = Estoque::find($id);
        //$this->classe->fill(['nome' => $request->nome]);
        $recurso->fill($request->all());
        $recurso->save();
        
        return $recurso;
    }

    public function destroy(int $id)
    {
        $qtdRecursosRemovidos = Estoque::destroy($id);
        return response()->json('',204);
    }

}