<?php

/** @var \Laravel\Lumen\Routing\Router $router */

use App\Models\Empresa;
use App\Models\User;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/status', function () {
    return response()
    ->json(
        [
            'msg' => 'OK',
        ],
        200
    );
});

$router->group(['prefix' => '{token}/api', 'middleware' => 'auth'], function() use ($router) {

    $router->post('/empresa', 'EmpresaController@createSchema');
    $router->get('/billing/check', 'BillingController@check');

    $router->get('/get-schema', function(Request $request) {
        $token = $request->route('token');
        $user =  User::where('api_token', $token)->first();
        $empresa = Empresa::where('id', $user->id_empresa)->first();

        return response()
        ->json(
            [
                'schema' => $empresa->base,
            ],
            200
        );
    });

    $router->group(['prefix' => '/produto'], function() use ($router) {
        $router->get('/search','ProdutoController@searchByField');
        $router->get('/search_exact','ProdutoController@searchExact');
        $router->get('', 'ProdutoController@index');
        $router->get('/{id}','ProdutoController@show');
        $router->post('', 'ProdutoController@store');
        $router->put('/{id}','ProdutoController@update');
        $router->delete('/{id}','ProdutoController@destroy');
    });

    $router->group(['prefix' => '/estoque'], function() use ($router) {
        $router->get('', 'EstoqueController@index');
        $router->get('/{id}','EstoqueController@show');
        $router->post('', 'EstoqueController@store');
        $router->put('/{id}','EstoqueController@update');
        $router->delete('/{id}','EstoqueController@destroy');
    });

    $router->group(['prefix' => '/venda'], function() use ($router) {
        $router->get('/pendente', 'VendaController@getVendasPendentes');
        $router->get('/search_exact','VendaController@searchExact');
        $router->get('', 'VendaController@index');
        $router->get('/{id}','VendaController@show');
        $router->post('', 'VendaController@store');
        $router->put('/{id}','VendaController@update');
        $router->delete('/{id}','VendaController@destroy');
    });

    $router->group(['prefix' => '/venda_produto'], function() use ($router) {
        $router->get('', 'VendaProdutoController@index');
        $router->get('/{id}','VendaProdutoController@show');
        $router->post('', 'VendaProdutoController@store');
        $router->put('/{id}','VendaProdutoController@update');
        $router->delete('/{id}','VendaProdutoController@destroy');
    });

    $router->group(['prefix' => '/relatorio'], function() use ($router) {
        $router->get('/periodo', 'RelatorioController@byPeriodo');
        $router->get('/categoria', 'RelatorioController@byCategoria');
        $router->get('/tipo_pagamento', 'RelatorioController@byTipoPagamento');
        $router->get('/mais_vendido', 'RelatorioController@maisVendido');
    });
});

$router->group(['prefix' => '/api'], function() use ($router) {

});

$router->group(['prefix' => '/api'], function() use ($router) {

});

$router->group(['prefix' => '/api'], function() use ($router) {

});

$router->group(['prefix' => '/api'], function() use ($router) {

});
