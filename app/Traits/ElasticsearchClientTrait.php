<?php

namespace App\Traits;

use Elasticsearch\ClientBuilder;

trait ElasticsearchClientTrait
{
    public $client;

    public function __construct()
    {
        $this->client = ClientBuilder::create()->build();
    }

    public function esSearch($params)
    {
        return $this->client->search($params);
    }

    public function esIndex(Array $data)
    {
        return $this->client->index($data);
    }

    public function esUpdate($data)
    {
        return $this->client->update($data);
    }

    public function esDelete($data)
    {
        return $this->client->delete($data);
    }
}