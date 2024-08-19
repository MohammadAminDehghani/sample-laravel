<?php

namespace App\Service;

use App\Models\ProfessorDetails;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\DomCrawler\Crawler;
use App\Models\ProfessorDetail;
use MongoDB\Client as MongoClient;

class WebScraperService
{
    protected $client;
    protected $mongoClient;

    public function __construct()
    {
        $this->client = new Client();
        $this->mongoClient = new MongoClient();
    }

    public function scrapeData(string $url, array $patterns)
    {
        // Fetch HTML content
        $response = $this->client->get($url);
        $htmlContent = $response->getBody()->getContents();

        // Save HTML to a file
        $this->saveHtmlToFile($url, $htmlContent);

        // Parse HTML content
        $data = $this->parseHtmlContent($htmlContent, $patterns);

        // Store data in MySQL and MongoDB
        $this->storeData($data);
    }

    protected function saveHtmlToFile(string $url, string $htmlContent)
    {
        $filename = 'html/' . md5($url) . '.html';
        Storage::disk('local')->put($filename, $htmlContent);
    }

    protected function parseHtmlContent(string $htmlContent, array $patterns)
    {
        $crawler = new Crawler($htmlContent);
        $data = [];

        foreach ($patterns as $key => $pattern) {
            $data[$key] = $crawler->filter($pattern)->each(function (Crawler $node) {
                return $node->text();
            });
        }

        return $data;
    }

    protected function storeData(array $data)
    {
        // Store data in MySQL
        //ProfessorDetails::create($data);

        // Store data in MongoDB
        $collection = $this->mongoClient->selectCollection('sample_laravel', 'professor_details');
        $collection->insertOne($data);
    }
}

