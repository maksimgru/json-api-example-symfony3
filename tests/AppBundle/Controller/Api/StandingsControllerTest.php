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

    public function testReturnValidJsonAndStatusCode()
    {
        // Without filter by date
        $this->client->request('GET', '/api/standings');
        $response = $this->client->getResponse()->getContent();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertJson($response, 'Invalid Json!!!');

        // With filter by date
        $this->client->request('GET', '/api/standings?from=2012-04-28&to=2012-04-29');
        $response = $this->client->getResponse()->getContent();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertJson($response, 'Invalid Json!!!');

        // Error invalid date format
        $this->client->request('GET', '/api/standings?from=2012:04:28');
        $response = $this->client->getResponse()->getContent();
        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
        $this->assertJson($response, 'Invalid Json!!!');
    }

    public function testReturnJsonAsCollectionOfAllTeamsAndSortedByPlace()
    {
        $this->client->request('GET', '/api/standings');
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

    public function testReturnJsonAsCollectionOfTeamsFilteredByCompetitionsDate()
    {
        // Filter by date ?from=&to=
        $this->client->request('GET', '/api/standings?from=2012-04-27&to=2012-04-28');
        $response = $this->client->getResponse()->getContent();
        $expectedJsonString = '
        [
            {"name":"FooTeam","place":1,"played":9,"wins":4,"draws":2,"losses":3,"points":20},
            {"name":"BarTeam","place":2,"played":7,"wins":3,"draws":1,"losses":3,"points":21}
        ]';
        $this->assertJsonStringEqualsJsonString($expectedJsonString, $response);

        // Filter by date from=&to= (out of range)
        $this->client->request('GET', '/api/standings?from=2012-04-01&to=2012-04-02');
        $response = $this->client->getResponse()->getContent();
        $expectedJsonString = '[]';
        $this->assertJsonStringEqualsJsonString($expectedJsonString, $response);

        // Filter by date ?from=
        $this->client->request('GET', '/api/standings?from=2012-04-28');
        $response = $this->client->getResponse()->getContent();
        $expectedJsonString = '
        [
            {"name":"FooTeam","place":1,"played":9,"wins":4,"draws":2,"losses":3,"points":20},
            {"name":"BarTeam","place":2,"played":7,"wins":3,"draws":1,"losses":3,"points":21},
            {"name":"DugTeam","place":3,"played":8,"wins":4,"draws":0,"losses":4,"points":22},
            {"name":"BugTeam","place":4,"played":9,"wins":4,"draws":2,"losses":3,"points":28}
        ]';
        $this->assertJsonStringEqualsJsonString($expectedJsonString, $response);

        // Filter by date ?to=
        $this->client->request('GET', '/api/standings?to=2012-04-28');
        $response = $this->client->getResponse()->getContent();
        $expectedJsonString = '
        [
            {"name":"FooTeam","place":1,"played":9,"wins":4,"draws":2,"losses":3,"points":20},
            {"name":"BarTeam","place":2,"played":7,"wins":3,"draws":1,"losses":3,"points":21}
        ]';
        $this->assertJsonStringEqualsJsonString($expectedJsonString, $response);
    }

    public function testReturnJsonWithErrorMessageInvalidDateFormat()
    {
        $this->client->request('GET', '/api/standings?from=2012:04:28');
        $response = $this->client->getResponse()->getContent();
        $expectedJsonString = '{"error":"Please, provide a valid date format like Y-m-d"}';
        $this->assertJsonStringEqualsJsonString($expectedJsonString, $response, 'Don\'t match response json string');
        $this->assertObjectHasAttribute('error', json_decode($response), 'Not exists "error" property!!!');
    }
}
