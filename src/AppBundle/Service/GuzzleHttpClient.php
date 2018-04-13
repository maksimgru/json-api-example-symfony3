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
    public function get(string $url): array
    {
        $response = $this->client->get($url);

        return json_decode($response->getBody()->getContents(), true);
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
