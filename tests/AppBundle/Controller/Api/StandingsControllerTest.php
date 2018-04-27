<?php

namespace Tests\AppBundle\Controller;

use AppBundle\DataFixtures\ORM\LoadCompetitionsTeamsData;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

class StandingsControllerTest extends WebTestCase
{
    /**
     * @var Client $client
     */
    private $client;

    public function setUp()
    {
        // Clear DB
        $this->loadFixtures([]);

        // Insert
        $this->loadFixtures([
            LoadCompetitionsTeamsData::class
        ]);

        $this->client = $this->makeClient();
    }

    /**
     * @param string $dateFrom
     * @param string $dateTo
     *
     * @dataProvider provideInputOutputData
     */
    public function testReturnValidJsonAndStatusCode(string $dateFrom, string $dateTo)
    {
        $uri = $this->buildUri($dateFrom, $dateTo);
        $this->client->request('GET', $uri);
        $response = $this->client->getResponse()->getContent();

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertJson($response, 'Invalid Json!!!');
    }

    public function testReturnJsonAsCollectionOfAllTeamsSortedByPlaceWithoutDateFilter()
    {
        $uri = $this->buildUri();
        $this->client->request('GET', $uri);
        $response = $this->client->getResponse()->getContent();

        $expectedJsonString = '
        [
            {"name":"FooTeam","place":1,"played":9,"wins":4,"draws":2,"losses":3,"points":20},
            {"name":"BarTeam","place":2,"played":7,"wins":3,"draws":1,"losses":3,"points":21},
            {"name":"DugTeam","place":3,"played":8,"wins":4,"draws":0,"losses":4,"points":22},
            {"name":"BugTeam","place":4,"played":9,"wins":4,"draws":2,"losses":3,"points":28}
        ]';

        $this->assertJsonStringEqualsJsonString($expectedJsonString, $response);
    }

    /**
     * @param string $dateFrom
     * @param string $dateTo
     * @param string $expectedJsonString
     *
     * @dataProvider provideInputOutputData
     */
    public function testReturnJsonAsCollectionOfTeamsFilteredByCompetitionsDate(string $dateFrom, string $dateTo, string $expectedJsonString)
    {
        $uri = $this->buildUri($dateFrom, $dateTo);
        $this->client->request('GET', $uri);
        $response = $this->client->getResponse()->getContent();

        $this->assertJsonStringEqualsJsonString($expectedJsonString, $response);
    }

    public function testReturnJsonWithErrorMessageInvalidDateFormat()
    {
        $uri = $this->buildUri('2012:04:28', '2012:04:29');
        $this->client->request('GET', $uri);
        $response = $this->client->getResponse()->getContent();
        $expectedJsonString = '{"error":"Please, provide a valid date format like Y-m-d"}';

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
        $this->assertJson($response, 'Invalid Json!!!');
        $this->assertJsonStringEqualsJsonString($expectedJsonString, $response);
        $this->assertObjectHasAttribute('error', json_decode($response), 'Not exists "error" property!!!');
    }

    public function provideInputOutputData(): array
    {
        return [
            // dateFrom, dateTo, expectedJsonString
            'from (empty) and to (empty)' => ['', '',
                '[
                    {"name":"FooTeam","place":1,"played":9,"wins":4,"draws":2,"losses":3,"points":20},
                    {"name":"BarTeam","place":2,"played":7,"wins":3,"draws":1,"losses":3,"points":21},
                    {"name":"DugTeam","place":3,"played":8,"wins":4,"draws":0,"losses":4,"points":22},
                    {"name":"BugTeam","place":4,"played":9,"wins":4,"draws":2,"losses":3,"points":28}
                ]'
            ],
            'from (in range) and to (in range)' => ['2012-04-28', '2012-04-29',
                '[
                    {"name":"FooTeam","place":1,"played":9,"wins":4,"draws":2,"losses":3,"points":20},
                    {"name":"BarTeam","place":2,"played":7,"wins":3,"draws":1,"losses":3,"points":21},
                    {"name":"DugTeam","place":3,"played":8,"wins":4,"draws":0,"losses":4,"points":22},
                    {"name":"BugTeam","place":4,"played":9,"wins":4,"draws":2,"losses":3,"points":28}
                ]'
            ],
            'from (out range) and to (in range)' => ['2012-04-27', '2012-04-28',
                '[
                    {"name":"FooTeam","place":1,"played":9,"wins":4,"draws":2,"losses":3,"points":20},
                    {"name":"BarTeam","place":2,"played":7,"wins":3,"draws":1,"losses":3,"points":21}
                ]'
            ],
            'from (in range) and to (empty)' => ['2012-04-28', '',
                '[
                    {"name":"FooTeam","place":1,"played":9,"wins":4,"draws":2,"losses":3,"points":20},
                    {"name":"BarTeam","place":2,"played":7,"wins":3,"draws":1,"losses":3,"points":21},
                    {"name":"DugTeam","place":3,"played":8,"wins":4,"draws":0,"losses":4,"points":22},
                    {"name":"BugTeam","place":4,"played":9,"wins":4,"draws":2,"losses":3,"points":28}
                ]'
            ],
            'from (empty) and to (in range)' => ['', '2012-04-28',
                '[
                    {"name":"FooTeam","place":1,"played":9,"wins":4,"draws":2,"losses":3,"points":20},
                    {"name":"BarTeam","place":2,"played":7,"wins":3,"draws":1,"losses":3,"points":21}
                ]'
            ],
            'from (out range) and to (out range)' => ['2010-04-01', '2010-04-02', '[]'],
        ];
    }

    /**
     * @param string $dateFrom
     * @param string $dateTo
     *
     * @return string
     */
    private function buildUri($dateFrom = '', $dateTo = ''): string
    {
        $params = [];

        if ('' !== $dateFrom) {
            $params[] = "from=$dateFrom";
        }

        if ('' !== $dateTo) {
            $params[] = "to=$dateTo";
        }

        $params = implode('&', $params);

        $uri = "/api/standings?$params";

        return $uri;
    }
}
