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

    public function getResponseFromURL($content, $model = 'gpt-3.5-turbo', $max_tokens = 1500)
    {
//        dump('content', $content);
//        dump('model', $model);
//        dump('max_tokens', $max_tokens);
        $response = $this->client->post('https://api.openai.com/v1/chat/completions', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => [
//                'model' => 'gpt-4o-2024-08-06',
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
        return $res_json['choices'][0]['message']['content'];
    }
}

