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

    public function getResponseFromURL($model = 'gpt-3.5-turbo', $content, $max_tokens = 1500)
    {
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
                        'content' => "Summarize the content at the following URL(give me the result as JSON file with keys like: name, last name, full name, email, phone, address, educations, ... if they are exist, and give me '' if not.): $url",
                    ],
                ],
                'max_tokens' => $max_tokens,
            ],
        ]);

        return json_decode($response->getBody(), true);
    }
}

