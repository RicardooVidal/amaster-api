<?php

namespace App\Util;

use App\Models\Empresa;
use App\Models\User;

// Classe centralizada para as funções do database
class Database {
    public static function changeSchema($token)
    {
        $schema = Database::getSchemaByToken($token);

        config(['database.connections.' . env('DB_CONNECTION') . '.schema' => $schema]);

        \DB::reconnect(env('DB_CONNECTION'));
    }

    public static function getSchemaByToken($token)
    {
        $user =  User::where('api_token', $token)->first();
        $empresa = Empresa::where('id', $user->id_empresa)->first();

        return $empresa->base;
    }

    public static function getSchemaByConfig()
    {
        $schema = config('database.connections.' . env('DB_CONNECTION') . '.schema');

        return $schema;
    }
}