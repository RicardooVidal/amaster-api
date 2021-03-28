<?php

namespace App\Http\Controllers;

use Exception;
use App\Util\Database;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BillingController extends Controller
{
    public function check()
    {
        $schema = Database::getSchemaByConfig();
        $today = Carbon::now()->toDateString();

        try {
            $billing = \DB::connection('mysql')->select('SELECT * FROM billing_amaster WHERE company = ' . "'" . $schema . "'");
            $billing = $billing[0];
            
            // Verifica se o usuário está em demonstração e checa o prazo de teste
            if ($billing->demo == 1) {
                if ($today > $billing->demo_expirate) {
                    return response()
                    ->json(
                        [
                            'msg' => 'Prazo de demonstração expirada! Para ativar a licença, favor entrar em contato em contato@ricardovidal.xyz.',
                            'status' => 'error'
                        ],
                        401
                    );
                }
            }

            if ($billing->expire != null) {
                // Se não for demo, verifica a licença normal
                if ($today > $billing->expire) {
                    return response()
                    ->json(
                        [
                            'msg' => 'Licença expirada, favor entrar em contato em contato@ricardovidal.xyz.',
                            'status' => 'error'
                        ],
                        401
                    );
                }
            }

            // Verifica se está ativo
            if (!$billing->active == 1) {
                return response()
                ->json(
                    [
                        'msg' => 'Usuário não ativo!',
                        'status' => 'error'
                    ],
                    401
                );
            }

            return response()
            ->json(
                [
                    'msg' => 'Licença Ok.',
                    'status' => 'ok'
                ],
                200
            );

        } catch (Exception $e) {
            return response()
            ->json(
                [
                    'msg' => 'Não foi possível validar a licença. Verifique sua conexão com a internet. Se o erro persistir, entre em contato em contato@ricardovidal.xyz',
                    'status' => 'error'
                ],
                401
            );
        }
    }

    public function store($schema)
    {
        $demo_expirate = Carbon::now()->addDays(30)->toDateString();

        $billing = \DB::connection('mysql')->insert(
            'INSERT INTO billing_amaster (company, value, demo, demo_expirate, active) VALUES (?, ?, ?, ?, ?)', 
            [$schema, '50.00', true , $demo_expirate, true]
        );

        // print_r($billing);
    }
}