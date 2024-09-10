<?php

namespace App\Services;

use GuzzleHttp\Client;

class OpenAIService
{
    protected $client;
    protected $apiKey;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = config('services.openai.key');
    }

    public function API_to_AI($content, $model = 'gpt-3.5-turbo', $max_tokens = 4000)
    {
        // if you want to pay more!
        //$model = 'gpt-4o-2024-08-06';

        $response = $this->client->post('https://api.openai.com/v1/chat/completions', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'model' => $model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $content,
                    ],
                ],
                'max_tokens' => $max_tokens,
            ],
        ]);
        $res_json =  json_decode($response->getBody(), true);
        //return $res_json;
        return $res_json['choices'][0]['message']['content'];
    }
}

