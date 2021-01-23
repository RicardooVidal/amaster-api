<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class EmpresaController extends Controller
{
    public function createSchema(Request $request)
    {
        try {
            Artisan::call('create-schema ' .$request->schema);
            return response()
            ->json(
                [
                    'msg' => 'Processo realizado com sucesso.',
                    'status' => 'ok'
                ],
                201
            );
        } catch(Exception $e) {
            return response()
            ->json(
                [
                    'msg' => $e->getMessage(),
                    'status' => 'error'
                ],
                500
            );
        }
    }
}