<?php

namespace AppBundle\Service;

use GuzzleHttp\Client;

/**
 * Class GuzzleHttpClient
 * @package AppBundle\Service
 *
 */
class GuzzleHttpClient implements HttpClientInterface
{
    /**
     * @var Client $client
     */
    private $client;

    /**
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @inheritDoc
     *
     * @throws \RuntimeException
     */
    public function get(string $url, array $options): array
    {
        $defaultOptions = [
            'type' => 'json',
        ];
        $options = array_merge($defaultOptions, $options);

        $response = $this->client->get($url);
        $content = $response->getBody()->getContents();

        switch ($options['type']) {
            case 'json':
                $content = json_decode($content, true);
                break;
            case 'html':
            case 'plain':
            default:
                break;
        }

        return [
            'content' => $content,
            'type'    => $options['type']
        ];
    }

    /**
     * @inheritDoc
     */
    public function post(string $url, array $data): mixed
    {
        // not in use, but we must define this method
        // as it is part of the HttpClientInterface interface
        return [];
    }
}
