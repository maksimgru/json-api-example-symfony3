<?php

namespace AppBundle\Service;

/**
 * interface HttpClientInterface
 * @package AppBundle\Service
 *
 */
interface HttpClientInterface
{
    /**
     * @param string $url
     *
     * @return array
     */
    public function get(string $url): array;

    /**
     * @param string $url
     * @param array $data
     *
     * @return mixed
     */
    public function post(string $url, array $data): mixed;
}
