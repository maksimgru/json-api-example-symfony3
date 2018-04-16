<?php

namespace AppBundle\Service;

use Symfony\Component\DomCrawler\Crawler;

/**
 * Class DomCrawlerService
 * @package AppBundle\Service
 *
 */
class DomCrawlerService extends Crawler
{
    /**
     * @param string $html
     *
     * @return DomCrawlerService
     */
    public static function getInstance(string $html): DomCrawlerService
    {
        return new self($html);
    }
}
