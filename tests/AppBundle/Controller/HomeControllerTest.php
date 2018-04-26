<?php

namespace Tests\AppBundle\Controller;

use AppBundle\DataFixtures\ORM\LoadCompetitionsTeamsData;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

class HomeControllerTest extends WebTestCase
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

    public function testDisplayHeadingAndTableAndSearchForm()
    {
        $crawler = $this->client->request('GET', '/');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertContains('Welcome to Symfony', $crawler->filter('#container #welcome h1')->text());
        $this->assertCount(1, $crawler->filter('form#search input[name="keyword"]'));
        $this->assertCount(2, $crawler->filter('table.table tbody tr'));
    }


    public function testSuccessFilterCompetitionsByTeamName()
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', '/');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Search')->form();
        $form['keyword']->setValue('FooTeam');
        $crawler = $this->client->submit($form);

        $this->assertCount(1, $crawler->filter('table.table tbody tr'));

        $this->assertContains(
            'Filtered Matches by Team name with "FooTeam"',
            $crawler->filter('body')->html()
        );
    }

    public function testErrorFilterCompetitionsByTeamName()
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', '/');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Search')->form();
        $form['keyword']->setValue('F');
        $crawler = $this->client->submit($form);

        $this->assertCount(2, $crawler->filter('table.table tbody tr'));

        $this->assertContains(
            'Please, provide min 2 characters!!!',
            $crawler->filter('body')->html()
        );
    }
}
