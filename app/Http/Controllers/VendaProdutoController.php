<?php

namespace App\Http\Controllers;

use App\Models\VendaProduto;
use Illuminate\Http\Request;

class VendaProdutoController extends Controller
{
    public function store(Request $request)
    {
        return response()
            ->json(
                // $this->classe::create($request->all()),
                VendaProduto::create([
                    'id_venda' => $request->id_venda,
                    'id_produto' => $request->id_produto,
                    'quantidade' => $request->quantidade,
                    'preco' => $request->preco,
                    'valor_total' => $request->valor_total
                ]),
                201
            );
    }

    public function show(int $id)
    {
        $recurso = VendaProduto::find($id);
        return response()->json($recurso);
    }

    public function update(int $id, Request $request)
    {
        $recurso = VendaProduto::find($id);
        //$this->classe->fill(['nome' => $request->nome]);
        $recurso->fill($request->all());
        $recurso->save();
        
        return $recurso;
    }

    public function destroy(int $id)
    {
        $qtdRecursosRemovidos = VendaProduto::destroy($id);
        return response()->json('',204);
    }

}