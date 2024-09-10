<?php

namespace App\Services;

use App\Models\Department;
use App\Models\Professor;
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

    public function scrapeDepartment(Department $department)
    {
        try {
            $response = $this->client->get($department->professors_url);
            if ($response->getStatusCode() == 200){
                $department->update(['url_response' => 200]);
                $htmlContent = $response->getBody()->getContents();
                $this->saveHtmlToFile($department, $htmlContent);
            } else {
                // Handle unexpected status codes here
                throw new \Exception("Unexpected status code: " . $response->getStatusCode());
            }
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            // Handle client errors (4xx responses)
            dump("Client error: " . $e->getMessage());
            $department->update(['url_response' => $e->getCode()]);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            // Handle other request errors
            echo "Request error: " . $e->getMessage();
        } catch (\Exception $e) {
            // Handle any other errors
            echo "General error: " . $e->getMessage();
        }

    }

    public function scrapeProfessor(Professor $professor)
    {
        try {
            $response = $this->client->get($professor->url);
            if ($response->getStatusCode() == 200){
                $professor->update(['url_response' => 200]);
                $htmlContent = $response->getBody()->getContents();
                $this->saveHtmlToFile($professor, $htmlContent);
            } else {
                // Handle unexpected status codes here
                throw new \Exception("Unexpected status code: " . $response->getStatusCode());
            }
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            // Handle client errors (4xx responses)
            dump("Client error: " . $e->getMessage());
            $professor->update(['url_response' => $e->getCode()]);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            // Handle other request errors
            echo "Request error: " . $e->getMessage();
        } catch (\Exception $e) {
            // Handle any other errors
            echo "General error: " . $e->getMessage();
        }

    }

//    protected function saveHtmlToFile(string $url, string $htmlContent)
//    {
//        $filename = 'html/' . md5($url) . '.html';
//        Storage::disk('local')->put($filename, $htmlContent);
//    }

    protected function saveHtmlToFile($object, string $htmlContent)
    {
        //$filename = 'html/' . md5($url) . '.html';
        $modelName = class_basename(get_class($object));
        $modelNameLower = strtolower($modelName); // This will give 'user'
        $filename = 'html/' . $modelNameLower . '/' . $object->id . '.html';
        Storage::disk('local')->put($filename, $htmlContent);
    }

    // Not Used!
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

    // Not Used!
    protected function storeData(array $data)
    {
        // Store data in MySQL
        //ProfessorDetails::create($data);

        // Store data in MongoDB
        $collection = $this->mongoClient->selectCollection('sample_laravel', 'professor_details');
        $collection->insertOne($data);
    }

    public function cleanHtmlForAI(string $url): array|string|null
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

        // Get the cleaned HTML content
        $cleanedHtml = $crawler->html();

        // Remove extra whitespace
        return preg_replace('/\s+/', ' ', $cleanedHtml);
    }

    public function cleanHtmlProfessorPageForAI(string $url): array|string|null
    {
        // Initialize the Crawler
        $crawler = new Crawler();

        // Fetch the HTML content
        $htmlContent = file_get_contents($url);

        // Add the HTML content to the Crawler
        $crawler->addHtmlContent($htmlContent);


        $crawler->filter('*')->each(function (Crawler $node) {
            $domNode = $node->getNode(0);

            if ($domNode) {
                // Remove <head>, <script>, <header>, and <footer> tags
                if (in_array($domNode->nodeName, ['head', 'script', 'header', 'footer'])) {
                    if ($domNode->parentNode) {
                        $domNode->parentNode->removeChild($domNode);
                    }
                    return; // Skip further processing for these nodes
                }

                // Remove all attributes except 'href' and 'src'
                $attributes = iterator_to_array($domNode->attributes);
                foreach ($attributes as $attr) {
                    // Retain 'href' for <a> tags and 'src' for <img> tags
                    if (!($domNode->nodeName === 'a' && $attr->nodeName === 'href') &&
                        !($domNode->nodeName === 'img' && $attr->nodeName === 'src')) {
                        $domNode->removeAttribute($attr->nodeName);
                    }
                }

                // If the node is empty (no text or children) and it's not an <img> or <link> tag, remove it
                if (!$domNode->hasChildNodes() && trim($domNode->textContent) === '' && !in_array($domNode->nodeName, ['img', 'link'])) {
                    if ($domNode->parentNode) {
                        $domNode->parentNode->removeChild($domNode);
                    }
                }
            }
        });



        // Get the cleaned HTML content
        $cleanedHtml = $crawler->html();

        // Remove extra whitespace
        return $this->convertToFragments(preg_replace('/\s+/', ' ', $cleanedHtml));
    }

    function convertToFragments($html) {
        // Define the tags to be converted to fragments
        $tagsToConvert = ['div', 'span', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'];

        // Loop through each tag and perform the replacement
        foreach ($tagsToConvert as $tag) {
            // Replace opening tags <tag> with <>
            $html = preg_replace("/<$tag\b[^>]*>/i", "<><>", $html);
            // Replace closing tags </tag> with </>
            $html = preg_replace("/<\/$tag>/i", "</>", $html);
        }

        return $html;
    }

}

