<?php

namespace App\Util;

class ElasticSearchConnection 
{
    private const RETRY = 10;

    // Verifica a conexão com o elasticsearch
    public static function wait()
    {
        $retries = 0;

        do {
            try {
                $response = json_decode(file_get_contents('http://localhost:9200'));
                if (!empty($response)) {
                    return response()
                        ->json(
                            [
                                'msg' => 'Elastisearch OK.',
                                'status' => 'ok'
                            ]
                            , 200
                        );
                }    
            } catch (\Exception $e) {
                echo 'Elasticsearch não subiu, tentando novamente...' . PHP_EOL . PHP_EOL;
                sleep(10);
                $retries++;
            }
        } while (self::RETRY > $retries);

        return response()
            ->json(
                [
                    'msg' => 'Tempo limite excedido para conectar ao Elasticsearch.',
                    'status' => '401'
                ]
                , 500
            );
    }
}
