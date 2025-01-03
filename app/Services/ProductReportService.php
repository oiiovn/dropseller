<?php
namespace App\Services;

use GuzzleHttp\Client;

class ProductReportService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://salework.net/api/open/stock/v1/',
        ]);
    }

    public function getProductReport($timeStart, $timeEnd)
    {
        $response = $this->client->post('report/product', [
            'headers' => [
                'client-id' => '1605',
                'token' => '+AXBRK19RPa6MG5wxYOhD7BPUGgibb76FnxirVzkW/9FMf9nSmJIg9OINUDk8X5L',
            ],
            'json' => [
                'time_start' => $timeStart,
                'time_end' => $timeEnd,
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }
}