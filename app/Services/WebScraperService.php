<?php

namespace App\Services;

use App\Models\Department;
use App\Models\ProfessorDetails;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\DomCrawler\Crawler;
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

    public function scrapeDepartment(Department $department, string $url)
    {
        // Fetch HTML content
        //$url = 'https://www.concordia.ca/ginacody/bcee.html';

        try {
            $response = $this->client->get($department->professors_url);
            if ($response->getStatusCode() == 200){
                $department->update(['url_response' => 200]);
                $htmlContent = $response->getBody()->getContents();
                //$this->saveHtmlToFile($url, $htmlContent);
            } else {
                // Handle unexpected status codes here
                throw new \Exception("Unexpected status code: " . $response->getStatusCode());
            }
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            // Handle client errors (4xx responses)

//                    $urlWithoutHtml = preg_replace('/\.html$/', '', $department->url);
//                    $newUrl = $urlWithoutHtml . '/faculty-staff.html';
//                    $department->update(['professors_url' => $newUrl]);

            dump("Client error: " . $e->getMessage());
            $department->update(['url_response' => $e->getCode()]);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            // Handle other request errors
            echo "Request error: " . $e->getMessage();
        } catch (\Exception $e) {
            // Handle any other errors
            echo "General error: " . $e->getMessage();
        }

        // Parse HTML content
        //$data = $this->parseHtmlContent($htmlContent, $patterns);

        // Store data in MySQL and MongoDB
        //$this->storeData($data);
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

    public function cleanHtmlForAI(string $url)
    {
        // Initialize the Crawler
        $crawler = new Crawler();

        // Fetch the HTML content
        $htmlContent = file_get_contents($url);

        // Add the HTML content to the Crawler
        $crawler->addHtmlContent($htmlContent);

        // Remove all classes and IDs from the HTML elements

        $crawler->filter('*')->each(function (Crawler $node) {
            $domNode = $node->getNode(0);

            if ($domNode) {

                // Iterate through all attributes and remove them unless it's an href
                $attributes = iterator_to_array($domNode->attributes);
                foreach ($attributes as $attr) {
                    if ($attr->nodeName !== 'href') {
                        $domNode->removeAttribute($attr->nodeName);
                    }
                }

                // If the node has no text content and no href attribute, remove it
                if (!$domNode->hasAttribute('href') && trim($domNode->textContent) === '') {
                    $domNode->parentNode->removeChild($domNode);
                } else {
                    // If the node has children, apply the logic recursively to them
                    $childNodes = [];
                    foreach ($domNode->childNodes as $childNode) {
                        if ($childNode->nodeType === XML_ELEMENT_NODE || trim($childNode->textContent) !== '') {
                            $childNodes[] = $childNode;
                        }
                    }

                    if (count($childNodes) === 0) {
                        // Remove empty tags that have no text or child elements
                        $domNode->parentNode->removeChild($domNode);
                    }
                }
            }
        });

//        $crawler->filter('*')->each(function (Crawler $node) {
//            $domNode = $node->getNode(0);
//
//            // If the node exists and does not have an href attribute
//            if ($domNode && !$domNode->hasAttribute('href')) {
//                // Create a document fragment to hold the node's children
//                $fragment = $domNode->ownerDocument->createDocumentFragment();
//
//                // Loop through child nodes and append them to the fragment
//                while ($domNode->firstChild) {
//                    $fragment->appendChild($domNode->firstChild);
//                }
//
//                // Replace the original node with the fragment (preserves children with hrefs)
//                $domNode->parentNode->replaceChild($fragment, $domNode);
//            }
//        });

        // Get the cleaned HTML content
        $cleanedHtml = $crawler->html();
       // return $cleanedHtml;
        // Convert HTML to plain text
        //$plainText = strip_tags($cleanedHtml);

        // Remove extra whitespace
        $plainText = preg_replace('/\s+/', ' ', $cleanedHtml);

        // Send the cleaned HTML to the AI for processing
        // This can be done via an API call or any other method you prefer
        // Here, we'll just return the cleaned HTML as an example
        return $plainText;
    }

}

