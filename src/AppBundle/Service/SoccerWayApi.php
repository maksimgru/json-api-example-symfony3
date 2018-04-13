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
    public function getCompetitions(): array
    {
        $competitions = [];
        $competitionsData = $this->httpClient->get('/competitions');

        foreach ($competitionsData as $competition) {
            $competitions[] = [
                'home_team'  => $competition['home_team'],
                'away_team'  => $competition['away_team'],
                'score'      => $competition['score'],
                'start_at'   => $competition['start_at'],
            ];
        }

        return $competitions;
    }

    /**
     * @param string $teamName
     *
     * @return array
     */
    public function getTeam(string $teamName): array
    {
        $teamData = $this->httpClient->get('/teams/' . $teamName);

        return [
            'place'  => $teamData['home_team'],
            'team'   => $teamData['away_team'],
            'played' => $teamData['played'],
            'wins'   => $teamData['wins'],
            'draws'  => $teamData['draws'],
            'losses' => $teamData['losses'],
            'points' => $teamData['points'],
        ];
    }
}
