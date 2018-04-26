<?php

namespace AppBundle\Service;

/**
 * Class SoccerWayService
 * @package AppBundle\Service
 *
 */
class SoccerWayService
{
    /**
     * @var HttpClientInterface $httpClient
     */
    private $httpClient;

    /**
     * @var DomCrawlerService $crawler
     */
    private $crawler;

    /**
     * @param HttpClientInterface           $httpClient
     * @param DomCrawlerService             $crawler
     */
    public function __construct(HttpClientInterface $httpClient, DomCrawlerService $crawler)
    {
        $this->httpClient = $httpClient;
        $this->crawler = $crawler;
    }

    /**
     * @return array
     * @throws \RuntimeException
     */
    public function fetchCompetitionsData(): array
    {
        $url= '/national/england/premier-league/2011-2012/regular-season/r14829/matches';
        $options = ['type' => 'html'];
        $response = $this->httpClient->get($url, $options);

        $rawData = $this->crawler::getInstance($response['content']);

        $result = $rawData
            ->filter('body .content .block_competition_matches table.matches tbody tr')
            ->each(function (DomCrawlerService $tr) {
                $homeTeamLink = trim($tr->filter('td.team-a a')->attr('href'));
                $homeTeamData = $this->fetchTeamData($homeTeamLink);

                $awayTeamLink = trim($tr->filter('td.team-b a')->attr('href'));
                $awayTeamData = $this->fetchTeamData($awayTeamLink);

                $score = trim($tr->filter('td.score')->text());
                $startAt = trim($tr->filter('td.date')->text());

                return [
                    'home_team' => $homeTeamData,
                    'away_team' => $awayTeamData,
                    'score'     => $score,
                    'start_at'  => $startAt,
                ];
            });

        return $result;
    }

    /**
     * @param string $url
     *
     * @return array
     * @throws \RuntimeException
     */
    public function fetchTeamData(string $url): array
    {
        $url = rtrim($url, '/') . '/statistics';
        $options = ['type' => 'html'];
        $response = $this->httpClient->get($url, $options);
        $result = [];

        $rawData = $this->crawler::getInstance($response['content']);

        $tableData = $rawData
            ->filter('body .content .block_general_statistics-wrapper table.compare tbody tr')
            ->each(function (DomCrawlerService $tr) {
                return [
                    'key'   => trim($tr->filter('th')->text()),
                    'value' => trim($tr->filter('td.total')->text()),
                ];
            });

        foreach ($tableData as $pair) {
            $result[$pair['key']] = $pair['value'];
        }

        $result['Name'] = $rawData->filter('#subheading h1')->getNode(0) ? $rawData->filter('#subheading h1')->text() : $url;

        return $result;
    }
}
