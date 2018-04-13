<?php

namespace AppBundle\Service;

/**
 * Class SoccerWayApi
 * @package AppBundle\Service
 *
 */
class SoccerWayApi
{
    /**
     * @var HttpClientInterface $httpClient
     */
    private $httpClient;

    /**
     * @param HttpClientInterface $httpClient;
     */
    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @return array
     */
    public function getTestUsers(): array
    {
        return $this->httpClient->get('/users');
    }

    /**
     * @return array
     */
    public function getMatches(): array
    {
        $matchesData = $this->httpClient->get('/matches');

        return [
            'home_team'  => $matchesData['home_team'],
            'away_team'  => $matchesData['away_team'],
            'start_date' => $matchesData['start_date'],
        ];
    }

    /**
     * @param string $team
     *
     * @return array
     */
    public function getTeam(string $team): array
    {
        $teamData = $this->httpClient->get('/teams/' . $team);

        return [
            'place'  => $teamData['home_team'],
            'team'   => $teamData['away_team'],
            'played' => $teamData['start_date'],
            'wins'   => $teamData['start_date'],
            'draws'  => $teamData['start_date'],
            'losses' => $teamData['start_date'],
            'points' => $teamData['start_date'],
        ];
    }
}
